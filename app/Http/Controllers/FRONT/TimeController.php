<?php

namespace App\Http\Controllers\FRONT;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Times;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimeController extends Controller
{
    public function index(Request $request)
    {
        $rules = [
            'tour_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $query = Times::where('tour_id', $request->tour_id)->get();

        if ($query) {
            return ResponseFormatter::success($query, 'success');
        } else {
            return ResponseFormatter::error(null, 'failed');
        }

    }
}
