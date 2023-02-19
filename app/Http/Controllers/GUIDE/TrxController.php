<?php

namespace App\Http\Controllers\Guide;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrxDetailResource;
use App\Http\Resources\TrxResource;
use App\Http\Resources\TrxTotalResource;
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

         $trx = Transactions::when($start, function($query) use ($start, $end){
                                return $query->whereBetween('date', [$start, $end]);
                            })
                            ->where('guide_id', $user->id)
                            ->with('booking')
                            ->orderBy($sortBy, $direction)
                            ->paginate($per_page, ['*'], 'page', $page);

        if($trx) {
            return TrxResource::collection($trx);
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function getTotal(Request $request)
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

         $trx = Transactions::when($start, function($query) use ($start, $end){
                                return $query->whereBetween('date', [$start, $end]);
                            })
                            ->where('guide_id', $user->id)
                            ->with('cost')
                            ->orderBy($sortBy, $direction)
                            ->paginate($per_page, ['*'], 'page', $page);

        if($trx->isNotEmpty()) {
            $fee = 0;
            $collect = 0;
            $susuk = 0;
            $cost = 0;
            $add = 0;
            foreach($trx as $item){
                $guide_fee = $fee + $item->booking->guide_fee;
                $collect = $collect + ($item->booking->collect*15000);
                $additional = $add + $item->booking->additional_price;
                foreach($item->cost as $bill){
                    if($bill->is_susuk == true){
                        $susuk = $susuk + $bill->price;
                    }else{
                        $cost = $cost + $bill->price;
                    }
                }

            }

            $data = [
                'guide_fee' => 'IDR '. number_format($guide_fee, 0, '.', '.'),
                'collect' => 'IDR '.number_format($collect, 0, '.', '.'),
                'cost' => 'IDR '.number_format($cost, 0, '.', '.'),
                'susuk' => 'IDR '.number_format($susuk, 0, '.', '.'),
                'additional' => 'IDR '.number_format($additional, 0, '.', '.'),

            ];

            return $data;
        }else{
            $data = [
                'guide_fee' => 'IDR 0',
                'collect' => 'IDR 0',
                'cost' => 'IDR 0',
                'susuk' => 'IDR 0',
                'additional' => 'IDR 0',

            ];
            return $data;
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
            return ResponseFormatter::success(new TrxDetailResource($trx), 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
