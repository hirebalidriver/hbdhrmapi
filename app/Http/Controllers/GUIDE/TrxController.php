<?php

namespace App\Http\Controllers\Guide;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrxResource;
use App\Models\Bookings;
use App\Models\Transactions;
use Illuminate\Http\Request;

class TrxController extends Controller
{
    public function index(Request $request)
    {

        $user = auth()->guard('guide')->user();

        $per_page = $request->input('per_page', 50);
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

        // $trx = Transactions::where('guide_id', $user->id)
        //                     ->with('booking')
        //                     ->orderBy($sortBy, $direction)
        //                     ->paginate($per_page, ['*'], 'page', $page);

        // $trx = Bookings::when($start, function($query) use ($start, $end){
        //                         return $query->whereBetween('date', [$start, $end]);
        //                     })
        //                     ->where('guide_id', $user->id)
        //                     ->with('trx')
        //                     ->orderBy($sortBy, $direction)
        //                     ->paginate($per_page, ['*'], 'page', $page);

         $trx = Transactions::when($start, function($query) use ($start, $end){
                                return $query->whereBetween('date', [$start, $end]);
                            })
                            ->where('guide_id', $user->id)
                            ->with('booking')
                            ->orderBy($sortBy, $direction)
                            ->paginate($per_page, ['*'], 'page', $page);

        if($trx) {
            return ResponseFormatter::success($trx, 'success');
            // return TrxResource::collection($trx);
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
