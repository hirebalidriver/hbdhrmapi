<?php

namespace App\Http\Controllers\Guide;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Balances;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function balance()
    {
        $user = auth()->guard('guide')->user();
        if (!$user) return ResponseFormatter::error(null, 'not found user');

        $balance = Balances::where('guide_id', $user->id)->latest()->first();

        if($balance){
            return ResponseFormatter::success($balance, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }


}
