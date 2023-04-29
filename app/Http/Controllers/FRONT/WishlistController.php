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

        $package = Packages::find($request->package_id);
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

        $query = Wishlists::create([
            'package_id' => $request->package_id,
            'tour_id' => $request->tour_id,
            'time' => $time->time,
            'date' => $request->date,
            'adult' => $request->adult,
            'child' => $request->child,
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
                        ->where('people', '<=', $wishlist->adult)
                        ->where('people_end', '>=', $wishlist->adult)
                        ->first();
        $priceChild = Prices::where('tour_id', $wishlist->tour_id)
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

            $create = Bookings::insertGetId([
                'ref_id' => $ref,
                'package_id' => $wishlist->package_id,
                'tour_id' => $wishlist->tour_id,
                'guide_id' => 0,
                'date' => $wishlist->date,
                'time' => $wishlist->time,
                'supplier' => 'website',
                'status' => 1,
                'note' => $request->note,
                'name' => $name,
                'email' => $request->email,
                'phone' => $request->phone,
                'hotel' => $hotel,
                'status_payment' => 'collect',
                'collect' => 0,
                'created_by' => 0,
                'country' => $request->country,
                'adult' => $wishlist->adult,
                'child' => $wishlist->child,
                'price' => $totalPrice,
                'guide_fee' => $tour->price_guide,
                'down_payment' => 0,
                'is_multi_days' => 0,
                'is_custom' => 0,
                'paypalEmail' => $request->paypalEmail,
            ]);

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
}
