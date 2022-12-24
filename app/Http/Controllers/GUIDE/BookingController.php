<?php

namespace App\Http\Controllers\GUIDE;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GENERAL\ImageUploadController;
use App\Models\Bills;
use App\Models\Bookings;
use App\Models\Guides;
use App\Models\Transactions;
use Exception;
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
        $sortBy = $request->sortBy == null ? $sortBy = 'id' : $sortBy = $request->sortBy;
        $direction =$request->input('direction', 'DESC');

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
                    ->with('packages', 'guides', 'user', 'options')
                    ->where('guide_id', $user->id)
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
                        ->with('packages', 'guides', 'user', 'options')
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

        $query = Bookings::with('packages', 'guides', 'user', 'options')
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
            $booking->save();

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
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');


        DB::beginTransaction();

        try{

            $user = auth()->guard('guide')->user();
            if (!$user) return ResponseFormatter::error(null, 'not found user');

            if($request->photo != null || $request->photo != ''){
                $upload = ImageUploadController::upload($request->photo, $user->id, 'bill');
                $create = Bills::create([
                    'booking_id' => $request->booking_id,
                    'photo' => $upload,
                    'price' => $request->price,
                    'note' => $request->note,
                    'is_susuk' => $request->is_susuk,
                ]);
            }else{
                $create = Bills::create([
                    'booking_id' => $request->booking_id,
                    'price' => $request->price,
                    'note' => $request->note,
                    'is_susuk' => $request->is_susuk,
                ]);
            }

            $booking = Bookings::where('id', $request->booking_id)->first();
            $booking->bill_total = $booking->bill_total + $request->price;
            if($request->is_susuk) {
                $booking->susuk_guide = $booking->susuk_guide + ($request->price/2);
                $booking->susuk_hbd = $booking->susuk_hbd + ($request->price/2);
            }else{
                $booking->tiket_total = $booking->tiket_total + $request->price;
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


        $query = Bills::where('booking_id', $request->booking_id)->get();

        if ($query) {
            return ResponseFormatter::success($query, 'success');
        } else {
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function billDelete(Request $request)
    {

        $query = Bills::find($request->id);

        if ($query->delete()) {
            return ResponseFormatter::success(null, 'success');
        } else {
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
