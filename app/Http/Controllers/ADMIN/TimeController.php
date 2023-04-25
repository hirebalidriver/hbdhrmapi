<?php

namespace App\Http\Controllers\ADMIN;

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

    public function add(Request $request)
    {
        $rules = [
            'tour_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $query = Times::create([
            'tour_id' => $request->tour_id,
            'time' => $request->time,
        ]);

        if ($query) {
            return ResponseFormatter::success($query, 'success');
        } else {
            return ResponseFormatter::error(null, 'failed');
        }

    }

    public function delete(Request $request)
    {
        $rules = [
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $query = Times::where('id', $request->id)->delete();

        if ($query) {
            return ResponseFormatter::success($query, 'success');
        } else {
            return ResponseFormatter::error(null, 'failed');
        }

    }
}
