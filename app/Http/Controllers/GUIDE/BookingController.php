<?php

namespace App\Http\Controllers\GUIDE;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GENERAL\ImageUploadController;
use App\Models\Bills;
use App\Models\Bookings;
use App\Models\Guides;
use App\Models\Packages;
use App\Models\Tours;
use App\Models\Destinations;
use App\Models\Notification;
use App\Models\Transactions;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function refid(Request $request)
    {
        $query = Bookings::where('ref_id', $request->ref)
                            ->with('packages', 'guides', 'user', 'options')
                            ->first();

        if($query){
            return ResponseFormatter::success($query, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function index(Request $request)
    {
        $user = auth()->guard('guide')->user();

        $per_page = $request->input('per_page', 20);
        $page = $request->input('page', 1);
        $sortBy = 'date';
        $direction = 'ASC';

        if($request->date_from > $request->date_end){
            $start = $request->date_end;
            $end = $request->date_from;
        }else{
            $start = $request->date_from;
            $end = $request->date_end;
        }

        $guest_name = $request->guest_name;
        $supplier = $request->supplier;
        $status = $request->status;

        if($request->ref_id != '' || $request->ref_id != null) {
            $find = Bookings::where('ref_id', $request->ref_id)
                    // ->with('packages', 'guides', 'user', 'options')
                    ->withCount('notification')
                    ->where('guide_id', $user->id)
                    ->where(function ($query) {
                        $query->where('status', '=', 6)
                              ->orWhere('status', '=', 1)
                              ->orWhere('status', '=', 7);
                    })
                    ->orderBy($sortBy, $direction)
                    ->paginate($per_page, ['*'], 'page', $page);
        }else{

            $find = Bookings::when($start, function($query) use ($start, $end){
                            return $query->whereBetween('date', [$start, $end]);
                        })
                        ->when($supplier, function($query) use ($supplier){
                            return $query->where('supplier', $supplier);
                        })
                        ->when($status, function($query) use ($status){
                            return $query->where('status', $status);
                        })
                        ->when($guest_name, function($query) use ($guest_name){
                            return $query->where('name', 'LIKE', '%'.$guest_name.'%');
                        })
                        ->where('guide_id', $user->id)
                        ->where(function ($query) {
                            $query->Where('status', '=', 3)
                                  ->orWhere('status', '=', 6)
                                  ->orWhere('status', '=', 1)
                                  ->orWhere('status', '=', 7);
                        })
                        ->with('packages', 'guides', 'user', 'options')
                        ->withCount('notification')
                        ->orderBy($sortBy, $direction)
                        ->paginate($per_page, ['*'], 'page', $page);
        }

        if($find){
            return ResponseFormatter::success($find, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function detail(Request $request)
    {
        $user = auth()->guard('guide')->user();

        $notif = Notification::where('booking_id', $request->id)
                        ->where('guide_id', $user->id)
                        ->update(['is_open' => 1]);

        $query = Bookings::with('packages', 'guides', 'user', 'options')
                        ->withCount('notification')
                        ->where('guide_id', $user->id)
                        ->where('id', $request->id)
                        ->first();

        if($query){
            return ResponseFormatter::success($query, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function complateTour(Request $request)
    {
        $rules = [
            'booking_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');


        DB::beginTransaction();
        try{

            $user = auth()->guard('guide')->user();

            $booking = Bookings::where('id', $request->booking_id)
                            ->where('guide_id', $user->id)->first();
            if(!$booking) return ResponseFormatter::error(null, 'not found booking');

            $booking->status = 3;
            
            // $guide = Guides::find($booking->guide_id);
            // if(!$guide) return ResponseFormatter::error(null, 'guide not found');

            // TOUR AND OPTIONS
            // $package = Packages::where('id', $booking->package_id)->first();
            // $option = Tours::where('id', $booking->tour_id)->first();

            // $details = [
            //     'to' => $guide->email,
            //     'name' => $guide->name,
            //     'ref' => $booking->ref_id,
            //     'package' => $package->title,
            //     'option' => $option->title,
            //     'date' => Carbon::parse($booking->date)->format('M d Y'),
            //     'time' => $booking->time->format('H:m'),
            //     'supplier' => $booking->supplier,
            //     'note' => $booking->note,
            //     'guestName' => $booking->name,
            //     'phone' => $booking->phone,
            //     'hotel' => $booking->hotel,
            //     'status_payment' => $booking->status_payment,
            //     'collect' => $booking->collect,
            //     'country' => $booking->country,
            //     'adult' => $booking->adult,
            //     'child' => $booking->child,
            //     'price' => $booking->price,
            // ];

            
            $booking->save();

            // \App\Jobs\RequestCompleteGuideJob::dispatch($details);

            DB::commit();
            return ResponseFormatter::success(null, 'success');
        }catch(Exception) {
            DB::rollBack();
            return ResponseFormatter::error(null, 'failed');
        }

    }

    public function uploadBill(Request $request)
    {
        $rules = [
            'booking_id' => ['required'],
            'people' => ['required'],
            'destination_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');


        DB::beginTransaction();

        try{

            $user = auth()->guard('guide')->user();
            if (!$user) return ResponseFormatter::error(null, 'not found user');

            $desti = Destinations::where('id', $request->destination_id)->first();

            if($request->photo != null || $request->photo != ''){
                $upload = ImageUploadController::upload($request->photo, $user->id, 'bill');
                $create = Bills::create([
                    'booking_id' => $request->booking_id,
                    'photo' => $upload,
                    'price' => $request->price,
                    'people' => $request->people,
                    'note' => $request->note,
                    'destination_id' => $desti->id,
                    'destination_name' => $desti->name,
                    'is_susuk' => $desti->is_susuk,
                ]);
            }else{
                $create = Bills::create([
                    'booking_id' => $request->booking_id,
                    'price' => $request->price,
                    'people' => $request->people,
                    'note' => $request->note,
                    'destination_id' => $desti->id,
                    'destination_name' => $desti->name,
                    'is_susuk' => $desti->is_susuk,
                ]);
            }

            $price = $request->price * $request->people;

            $booking = Bookings::where('id', $request->booking_id)->first();
            
            if($desti->is_susuk) {
                $booking->susuk_guide = $booking->susuk_guide + ($request->price/2);
                $booking->susuk_hbd = $booking->susuk_hbd + ($request->price/2);
            }else{
                $booking->bill_total = $booking->bill_total + $price;
                $booking->tiket_total = $booking->tiket_total + $price;
            }
            $booking->save();


            DB::commit();
            return ResponseFormatter::success($create, 'success');

        }catch (Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function bills(Request $request)
    {
        $rules = [
            'booking_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');


        $query = Bills::where('booking_id', $request->booking_id)->where('is_susuk', 0)->get();

        if ($query) {
            return ResponseFormatter::success($query, 'success');
        } else {
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function susuk(Request $request)
    {
        $rules = [
            'booking_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');


        $query = Bills::where('booking_id', $request->booking_id)->where('is_susuk', 1)->get();

        if ($query) {
            return ResponseFormatter::success($query, 'success');
        } else {
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function billsTotalTicket(Request $request)
    {
        $rules = [
            'booking_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');


        $query = Bills::where('booking_id', $request->booking_id)->where('is_susuk', 0)->get();

        $total = 0;

        if($query->isNotEmpty()) {

            foreach($query as $item){
                $total = $total + ($item->people * $item->price);
            }

            $data = [
                'total' => number_format($total, 0, '.', '.'),

            ];

            return $data;
        }else{
            $data = [
                'total' => number_format($total, 0, '.', '.'),

            ];

            return $data;
        }

        
    }

    public function guideApproved(Request $request)
    {
        $rules = [
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');


        $booking = Bookings::where('id', $request->id)->first();
        $booking->status = 7;

        // $guide = Guides::find($booking->guide_id);
        // if(!$guide) return ResponseFormatter::error(null, 'guide not found');

        // TOUR AND OPTIONS
        // $package = Packages::where('id', $booking->package_id)->first();
        // $option = Tours::where('id', $booking->tour_id)->first();

        // $details = [
        //     'to' => $guide->email,
        //     'name' => $guide->name,
        //     'ref' => $booking->ref_id,
        //     'package' => $package->title,
        //     'option' => $option->title,
        //     'date' => Carbon::parse($booking->date)->format('M d Y'),
        //     'time' => $booking->time->format('H:m'),
        //     'supplier' => $booking->supplier,
        //     'note' => $booking->note,
        //     'guestName' => $booking->name,
        //     'phone' => $booking->phone,
        //     'hotel' => $booking->hotel,
        //     'status_payment' => $booking->status_payment,
        //     'collect' => $booking->collect,
        //     'country' => $booking->country,
        //     'adult' => $booking->adult,
        //     'child' => $booking->child,
        //     'price' => $booking->price,
        // ];

        
        $booking->save();

        // \App\Jobs\ApproveGuideJob::dispatch($details);

        if ($booking) {
            return ResponseFormatter::success($query, 'success');
        } else {
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function guideRejected(Request $request)
    {
        $rules = [
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');


        $booking = Bookings::where('id', $request->id)->first();
        $booking->status = 8;
        
        // $guide = Guides::find($booking->guide_id);
        // if(!$guide) return ResponseFormatter::error(null, 'guide not found');

        // TOUR AND OPTIONS
        // $package = Packages::where('id', $booking->package_id)->first();
        // $option = Tours::where('id', $booking->tour_id)->first();

        // $details = [
        //     'to' => $guide->email,
        //     'name' => $guide->name,
        //     'ref' => $booking->ref_id,
        //     'package' => $package->title,
        //     'option' => $option->title,
        //     'date' => Carbon::parse($booking->date)->format('M d Y'),
        //     'time' => $booking->time->format('H:m'),
        //     'supplier' => $booking->supplier,
        //     'note' => $booking->note,
        //     'guestName' => $booking->name,
        //     'phone' => $booking->phone,
        //     'hotel' => $booking->hotel,
        //     'status_payment' => $booking->status_payment,
        //     'collect' => $booking->collect,
        //     'country' => $booking->country,
        //     'adult' => $booking->adult,
        //     'child' => $booking->child,
        //     'price' => $booking->price,
        // ];

        
        $booking->save();

        // \App\Jobs\RejectedGuideJob::dispatch($details);

        if ($booking) {
            return ResponseFormatter::success($query, 'success');
        } else {
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function billDelete(Request $request)
    {

        $rules = [
            'booking_id' => ['required'],
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');


        DB::beginTransaction();

        try{
            $bill = Bills::find($request->id);

            $booking = Bookings::where('id', $request->booking_id)->first();
            
            if($bill->is_susuk) {
                $booking->susuk_guide = $booking->susuk_guide - ($bill->price/2);
                $booking->susuk_hbd = $booking->susuk_hbd - ($bill->price/2);
            }else{
                $booking->bill_total = $booking->bill_total - ($bill->price * $bill->people);
                $booking->tiket_total = $booking->tiket_total - ($bill->price * $bill->people);
            }
            $booking->save();
            $bill->delete();

            DB::commit();
            return ResponseFormatter::success(null, 'success');

        }catch (Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
