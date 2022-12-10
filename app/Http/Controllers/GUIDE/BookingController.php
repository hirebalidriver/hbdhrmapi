<?php

namespace App\Http\Controllers\GUIDE;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Bookings;
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

            $check = Transactions::where('booking_id', $request->booking_id)
                        ->where('guide_id', $user->id)->first();
            if($check) return ResponseFormatter::error(null, 'failed');

            Transactions::create([
                'booking_id' => $booking->id,
                'guide_id' => $booking->guide_id,
                'user_id' => 0,
                'price' => $booking->guide_fee,
                'status' => 0,
            ]);

            $booking->status = 3;
            $booking->save();

            DB::commit();
            return ResponseFormatter::success(null, 'success');
        }catch(Exception) {
            DB::rollBack();
            return ResponseFormatter::error(null, 'failed');
        }

    }
}
