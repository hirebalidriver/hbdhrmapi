<?php

namespace App\Http\Controllers\Guide;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Illuminate\Http\Request;

class TrxController extends Controller
{
    public function index(Request $request)
    {

        $user = auth()->guard('guide')->user();

        $per_page = $request->input('per_page', 20);
        $page = $request->input('page', 1);
        $sortBy = $request->sortBy == null ? $sortBy = 'id' : $sortBy = $request->sortBy;
        $direction = $request->direction!= null ? 'DESC' : 'ASC';

        $trx = Transactions::where('guide_id', $user->id)
                            ->with('booking')
                            ->orderBy($sortBy, $direction)
                            ->paginate($per_page, ['*'], 'page', $page);

        if($trx) {
            return ResponseFormatter::success($trx, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function detail(Request $request)
    {

        $user = auth()->guard('guide')->user();

        $trx = Transactions::where('guide_id', $user->id)
                            ->where('id', $request->id)
                            ->with('booking')
                            ->first();

        if($trx) {
            return ResponseFormatter::success($trx, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
