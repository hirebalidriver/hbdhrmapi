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
}
