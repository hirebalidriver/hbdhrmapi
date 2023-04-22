<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Prices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PricesController extends Controller
{
    public function index(Request $request)
    {
        $rules = [
            'tour_id' => ['required'],
            'type' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $query = Prices::where('tour_id', $request->tour_id)
                    ->where('type', $request->type)
                    ->orderBy('id', 'DESC')->get();

        if($query){
            return ResponseFormatter::success($query, 'success');
        }else{
            return ResponseFormatter::error(null, 'success');
        }
    }

    public function add(Request $request)
    {
        $rules = [
            'tour_id' => ['required'],
            'type' => ['required'],
            'min' => ['required'],
            'max' => ['required'],
            'price' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $query = Prices::create([
            'tour_id' => $request->tour_id,
            'type' => $request->type,
            'people' => $request->min,
            'people_end' => $request->max,
            'price' => $request->price,
            'is_active' => 1,
        ]);

        if($query){
            return ResponseFormatter::success($query, 'success');
        }else{
            return ResponseFormatter::error(null, 'success');
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

        $query = Prices::where('id', $request->id)->first();

        if($query->delete()){
            return ResponseFormatter::success(null, 'success');
        }else{
            return ResponseFormatter::error(null, 'success');
        }
    }
}
