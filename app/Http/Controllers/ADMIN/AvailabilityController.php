<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Availability;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {

        $start = Carbon::parse($request->date);
        $end = Carbon::parse($request->date)->addDays(30);


         $query = Availability::when($start, function($query) use ($start, $end){
                                return $query->whereBetween('date', [$start, $end]);
                            })->get();

        if($query) {
            return ResponseFormatter::success($query, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
