<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Bills;
use App\Models\Bookings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BillController extends Controller
{

    public function index(Request $request)
    {
        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $sortBy = $request->sortBy == null ? $sortBy = 'id' : $sortBy = $request->sortBy;
        $direction =$request->input('direction', 'DESC');

        $bookings = Bookings::with('packages', 'guides', 'user', 'options')->where('status', '>=', 2)
                        ->orderBy($sortBy, $direction)
                        ->paginate($per_page, ['*'], 'page', $page);

        if($bookings){
            return ResponseFormatter::success($bookings, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function detail(Request $request)
    {

        $rules = [
            'booking_id' => ['required']
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $booking = Bookings::where('id', $request->booking_id)->first();

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
