<?php

namespace App\Http\Controllers\GUIDE;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Bookings;
use Illuminate\Http\Request;
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
}
