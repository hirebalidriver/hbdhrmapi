<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GENERAL\ImageUploadController;
use App\Models\Bills;
use App\Models\Bookings;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BillController extends Controller
{

    public function index(Request $request)
    {
        $rules = [
            'booking_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $sortBy = $request->sortBy == null ? $sortBy = 'id' : $sortBy = $request->sortBy;
        $direction =$request->input('direction', 'DESC');

        $bookings = Bills::where('booking_id', $request->booking_id)
                        ->orderBy($sortBy, $direction)
                        ->paginate($per_page, ['*'], 'page', $page);

        if($bookings){
            return ResponseFormatter::success($bookings, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function add(Request $request)
    {
        $rules = [
            'booking_id' => ['required'],
            'people' => ['required'],
            'price' => ['required'],
            'is_susuk' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        DB::beginTransaction();

        try{

            $booking = Bookings::where('id', $request->booking_id)->first();
            if(!$booking){
                return ResponseFormatter::error(null, 'not found');
            }

            $upload = '';

            if($request->photo != null || $request->photo != ''){
                $upload = ImageUploadController::upload($request->photo, $booking->guide_id, 'bill');
            }

            $create = Bills::create([
                'booking_id' => $request->booking_id,
                'destination_id' => $request->destination_id,
                'destination_name' => $request->destination_name,
                'people' => $request->people,
                'photo' => $upload,
                'price' => $request->price,
                'note' => $request->note ? $request->note : null,
                'is_susuk' => $request->is_susuk,
            ]);

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

    public function delete(Request $request)
    {
        $rules = [
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        DB::beginTransaction();

        try{

            $bill = Bills::find($request->id);
            if(!$bill){
                return ResponseFormatter::error(null, 'not found');
            }

            $booking = Bookings::find($bill->booking_id);
            if(!$booking){
                return ResponseFormatter::error(null, 'not found');
            }

            $booking->bill_total = $booking->bill_total - $bill->price;

            if($bill->is_susuk) {
                $booking->susuk_guide = $booking->susuk_guide - ($bill->price/2);
                $booking->susuk_hbd = $booking->susuk_hbd - ($bill->price/2);
            }else{
                $booking->tiket_total = $booking->tiket_total - $bill->price;
            }

            $booking->save();

            $bill->delete();

            DB::commit();
            return ResponseFormatter::success($bill, 'success');

        }catch (Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error(null, 'failed');
        }

    }

    public function detail(Request $request)
    {

        $rules = [
            'booking_id' => ['required'],

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $booking = Bookings::where('id', $request->booking_id)
                            ->with('packages', 'guides', 'user', 'options')
                            ->first();

        $query = Bills::where('booking_id', $request->booking_id)
                        ->orderBy('id', 'desc')
                        ->get();

        $data = [
            'bills' => $query,
            'booking' => $booking
        ];

        if($query){
            return ResponseFormatter::success($data, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function filter(Request $request)
    {

        $per_page = $request->input('per_page', 10);
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
                        ->with('packages', 'guides', 'user', 'options')
                        ->orderBy($sortBy, $direction)
                        ->paginate($per_page, ['*'], 'page', $page);
        }

        if($find) {
            return ResponseFormatter::success($find, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
