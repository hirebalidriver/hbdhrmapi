<?php

namespace App\Http\Controllers\FRONT;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Packages;
use App\Models\Prices;
use App\Models\Times;
use App\Models\Tours;
use App\Models\Wishlists;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class WishlistController extends Controller
{
    public function detail(Request $request)
    {
        $rules = [
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $find = Wishlists::where('id', $request->id)->with('tour', 'option')
                        ->first();

        $priceAdult = Prices::where('tour_id', $find->tour_id)
                        ->where('type', 1)
                        ->where('people', '<=', $find->adult)
                        ->where('people_end', '>=', $find->adult)
                        ->first();
        $priceChild = Prices::where('tour_id', $find->tour_id)
                        ->where('type', 2)
                        ->where('people', '<=', $find->child)
                        ->where('people_end', '>=', $find->child)
                        ->first();

        $data = [
            'data' => $find,
            'priceAdults' => $priceAdult,
            'priceChild' => $priceChild
        ];

        if($find) {
            return ResponseFormatter::success($data, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function add(Request $request)
    {
        $rules = [
            'package_id' => ['required'],
            'tour_id' => ['required'],
            'time_id' => ['required'],
            'date' => ['required'],
            'adult' => ['required'],
            'child' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $package = Packages::where('tour_code',$request->package_id)->first();
        if(!$package) {
            return ResponseFormatter::error(null, 'Tour not found');
        }
        $tour = Tours::find($request->tour_id);
        if(!$tour) {
            return ResponseFormatter::error(null, 'Option not found');
        }

        $time = Times::find($request->time_id);
        if(!$time) {
            return ResponseFormatter::error(null, 'Time not found');
        }

        $priceAdult = Prices::where('tour_id', $request->tour_id)
                    ->where('type', 1)
                    ->where('people', '<=', $request->adult)
                    ->where('people_end', '>=', $request->adult)
                    ->first();
        $priceChild = Prices::where('tour_id', $request->tour_id)
                    ->where('type', 2)
                    ->where('people', '<=', $request->child)
                    ->where('people_end', '>=', $request->child)
                    ->first();

        $dateObject = Carbon::createFromFormat('Y-d-m', $request->date);
        $date       = Carbon::parse($dateObject)->format("Y-m-d");

        $query = Wishlists::create([
            'package_id' => $package->id,
            'tour_id' => $tour->id,
            'time' => $time->time,
            'date' => $date,
            'adult' => $request->adult,
            'child' => $request->child,
            'adult_price' => $priceAdult->price,
            'child_price' => $priceChild->price,
        ]);

        if($query) {
            return ResponseFormatter::success($query, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function booking(Request $request)
    {
        $rules = [
            'wishlist_id' => ['required'],
            'fname' => ['required'],
            'lname' => ['required'],
            'email' => ['required', 'email'],
            'phone' => ['required'],
            'address' => ['required'],
            'country' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }


        $wishlist = Wishlists::where('id', $request->wishlist_id)->first();
        if(!$wishlist) {
            return ResponseFormatter::error(null, 'Wishlist not found');
        }

        $package = Packages::find($wishlist->package_id);
        if(!$package) {
            return ResponseFormatter::error(null, 'Tour not found');
        }

        $tour = Tours::find($wishlist->tour_id);
        if(!$tour) {
            return ResponseFormatter::error(null, 'Option not found');
        }

        $priceAdult = Prices::where('tour_id', $wishlist->tour_id)
                        ->where('type', 1)
                        ->where('people', '<=', $wishlist->adult)
                        ->where('people_end', '>=', $wishlist->adult)
                        ->first();
        $priceChild = Prices::where('tour_id', $wishlist->tour_id)
                        ->where('type', 2)
                        ->where('people', '<=', $wishlist->child)
                        ->where('people_end', '>=', $wishlist->child)
                        ->first();
        DB::beginTransaction();
        try{
            // CREATE BOOKING
            $now = Carbon::now()->format('YdHm');
            $ref = 'WEB'.$now.$wishlist->id;
            $name = $request->fname.' '.$request->lname;
            $hotel = $request->hotel.' '.$request->hotel_address;
            $totalPrice = ($wishlist->adult * $priceAdult->price) + ($wishlist->child * $priceChild->price);

            // TOUR AND OPTIONS
            $tour = Packages::where('id', $wishlist->package_id)->first();
            $option = Tours::where('id', $wishlist->tour_id)->first();

            if($request->orderId != null || $request->orderId != '') {
                $payment = 'paid';
                $collect = 0;
            }else{
                $payment = 'collect';
                $collect = $totalPrice;
            }

            $create = Bookings::insertGetId([
                'ref_id' => $ref,
                'package_id' => $tour->id,
                'tour_id' => $option->id,
                'guide_id' => 0,
                'date' => $wishlist->date,
                'time' => $wishlist->time,
                'supplier' => 'website',
                'status' => 0,
                'note' => $request->note,
                'name' => $name,
                'email' => $request->email,
                'phone' => $request->phone,
                'hotel' => $hotel,
                'status_payment' => $payment,
                'collect' => $collect,
                'created_by' => 0,
                'country' => $request->country,
                'adult' => $wishlist->adult,
                'child' => $wishlist->child,
                'adult_price' => $wishlist->adult_price,
                'child_price' => $wishlist->child_price,
                'price' => $totalPrice,
                'guide_fee' => $tour->price_guide,
                'down_payment' => 0,
                'is_multi_days' => 0,
                'is_custom' => 0,
                'paypalEmail' => $request->paypalEmail,
                'order_id' => $request->orderId,
            ]);

            $dateObject = Carbon::createFromFormat('Y-m-d', $wishlist->date);
            $date       = Carbon::parse($dateObject)->format("M d Y");

            $details = [
                'name' => $name,
                'tour' => $tour->title,
                'option' => $option->title,
                'ref' => $ref,
                'date' => $date,
                'time' => $wishlist->time,
                'adult' => $wishlist->adult,
                'child' => $wishlist->child,
                'adult_price' => $wishlist->adult_price,
                'child_price' => $wishlist->child_price,
                'adult_total' => $wishlist->adult * $wishlist->adult_price,
                'child_total' => $wishlist->child * $wishlist->child_price,
                'total' => $totalPrice,
                'payment' => $payment == 'collect' ? 'Pay Later' : 'Pay Now - Paypal ('.$request->orderId.')',
                'note' => $request->note,
                'phone' => $request->phone,
                'email' => $request->email,
                'country' => $request->country,
                'hotel' => $hotel,
            ];

            \App\Jobs\BookingCustomerJob::dispatch($details);
            \App\Jobs\BookingAdminJob::dispatch($details);


            DB::commit();

            return ResponseFormatter::success($create, 'success');

        }catch(Exception $e){
            DB::rollback();
            return ResponseFormatter::error(null, 'failed');
        }

    }

    public function findBooking(Request $request)
    {
        $rules = [
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $booking = Bookings::where('id', $request->id)
                    ->with('packages', 'guides', 'user', 'options')
                    ->first();

        if($booking) {
            return ResponseFormatter::success($booking, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function sendEmail(Request $request)
    {
        $rules = [
            'id' => ['required']
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $booking = Bookings::where('id', $request->id)->first();

        // TOUR AND OPTIONS
        $tour = Packages::where('id', $booking->package_id)->first();
        $option = Tours::where('id', $booking->tour_id)->first();

        $dateObject = Carbon::createFromFormat('Y-m-d', $booking->date);
        $date       = Carbon::parse($dateObject)->format("M d Y");

        $details = [
            'name' => $booking->name,
            'tour' => $tour->title,
            'option' => $option->title,
            'ref' => $booking->ref_id,
            'date' => $date,
            'time' => $booking->time,
            'adult' => $booking->adult,
            'child' => $booking->child,
            'adult_price' => $booking->adult_price,
            'child_price' => $booking->child_price,
            'adult_total' => $booking->adult * $booking->adult_price,
            'child_total' => $booking->child * $booking->child_price,
            'total' => $booking->price,
            'payment' => $booking->status_payment == 'collect' ? 'Pay Later' : 'Pay Now',
            'note' => $booking->note,
            'phone' => $booking->phone,
            'email' => $booking->email,
            'country' => $booking->country,
            'hotel' => $booking->hotel,
        ];

        \App\Jobs\BookingCustomerJob::dispatch($details);

        if($booking) {
            return ResponseFormatter::success($booking, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }

    }
}
