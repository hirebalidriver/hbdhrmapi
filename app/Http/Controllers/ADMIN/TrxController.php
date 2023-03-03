<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Balances;
use App\Models\Bookings;
use App\Models\Guides;
use App\Models\Transactions;
use App\Services\FCMService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrxController extends Controller
{
    public function bookingApprove(Request $request)
    {
        $rules = [
            'booking_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');


        DB::beginTransaction();

        try{
            $user = Auth::user();

            $booking = Bookings::where('id', $request->booking_id)
                            ->where('status', 2)
                            ->orWhere('status', 3)->first();
            if(!$booking) return ResponseFormatter::error(null, 'not found booking or booking has been completed');

            //TOTAL FEE
            $total = ($booking->guide_fee + $booking->tiket_total) -  $booking->susuk_hbd - $booking->collect;
            // $total = $booking->guide_fee + ($booking->bill_total - $booking->susuk_hbd - $booking->collect);

            $trxID =  Transactions::insertGetId([
                'booking_id' => $booking->id,
                'guide_id' => $booking->guide_id,
                'user_id' => $user->id,
                'price' => $total,
                'status' => 1,
                'date' => Carbon::now(),
            ]);

            //BALANCE
            $trx = Transactions::where('id', $trxID)->first();
            $balance = Balances::where('guide_id', $booking->guide_id)->latest()->first();
            Balances::create([
                'guide_id' => $trx->guide_id,
                'balance' => $balance->balance+$trx->price,
                'trx_id' => $trx->id,
                'in' => $trx->price,
                'out' => 0,
                'fee' => 0,
                'type' => 'tour',
            ]);

            $booking->status = 4;
            $booking->save();


            DB::commit();
            return ResponseFormatter::success(null, 'success');

        }catch(Exception) {
            DB::rollback();
            return ResponseFormatter::error(null, 'failed');
        }

    }

    public function bookingNotApprove(Request $request)
    {
        $rules = [
            'booking_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');


        DB::beginTransaction();

        try{
            $user = Auth::user();

            $booking = Bookings::where('id', $request->booking_id)->first();
            if(!$booking) return ResponseFormatter::error(null, 'not found booking');

            $booking->status = 5;
            $booking->save();

            DB::commit();
            return ResponseFormatter::success(null, 'success');

        }catch(Exception) {
            DB::rollback();
            return ResponseFormatter::error(null, 'failed');
        }

    }
}
