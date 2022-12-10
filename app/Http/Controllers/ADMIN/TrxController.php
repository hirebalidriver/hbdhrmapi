<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Balances;
use App\Models\Bookings;
use App\Models\Transactions;
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
            'trx_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');


        DB::beginTransaction();

        try{
            $user = Auth::user();

            $trx = Transactions::where('id', $request->trx_id)->first();
            $trx->user_id = $user->id;
            $trx->status = 1;
            $trx->save();

            $balance = Balances::where('guide_id', $trx->guide_id)->latest()->first();

            Balances::create([
                'guide_id' => $trx->guide_id,
                'balance' => $balance->balance+$trx->price,
                'trx_id' => $trx->id,
                'in' => $trx->price,
                'out' => 0,
                'fee' => 0,
                'type' => 'tour',
            ]);

            Bookings::where('id', $trx->booking_id)->update([
                'status' => 3
            ]);

            DB::commit();
            return ResponseFormatter::success(null, 'success');

        }catch(Exception) {
            DB::rollback();
            return ResponseFormatter::error(null, 'failed');
        }

    }

    public function trxNotApprove(Request $request)
    {
        $rules = [
            'trx_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');


        DB::beginTransaction();

        try{
            $user = Auth::user();

            $trx = Transactions::where('id', $request->trx_id)->first();
            $trx->user_id = $user->id;
            $trx->status = 2;
            $trx->save();

            DB::commit();
            return ResponseFormatter::success(null, 'success');

        }catch(Exception) {
            DB::rollback();
            return ResponseFormatter::error(null, 'failed');
        }

    }
}
