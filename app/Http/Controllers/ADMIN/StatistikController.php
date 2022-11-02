<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Guides;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function count(Request $reqeust)
    {
        // dd('count');
        $guides = DB::table('guides')->count();
        $tours = DB::table('tours')->count();
        $bookings = DB::table('bookings')->count();

        $data = [
            'guides'=> $guides,
            'tours'=> $tours,
            'bookings'=> $bookings
        ];

        return ResponseFormatter::success($data, 'success');
    }
}
