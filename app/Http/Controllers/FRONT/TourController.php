<?php

namespace App\Http\Controllers\FRONT;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\BlackPeriod;
use App\Models\Packages;
use App\Models\Tours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TourController extends Controller
{
    public function tourByID(Request $request)
    {
        $rules = [
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $find = Packages::where('tour_code', $request->id)->first();

        if($find) {
            return ResponseFormatter::success($find, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function optionsByTourID(Request $request)
    {
        $rules = [
            'id' => ['required'],
            // 'type' => ['required'],
            // 'people' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $package = Packages::where('tour_code', $request->id)->first();

        $find = Tours::select('tours.*')
                ->join('package_relations', 'package_relations.tour_id', 'tours.id')
                ->where('package_relations.package_id', $package->id)
                ->with(['prices', 'times'])
                ->where('tours.status', 1)->get();

        // Transform the result using TourResource
        $transformedData = $find->map(function ($tour) {
            return new \App\Http\Resources\TourResource($tour);
        });


        if($transformedData) {
            return ResponseFormatter::success($transformedData, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function getBlackPeriod(Request $request)
    {
        $get = BlackPeriod::where('date', '>=', now())->get();

        if($get) {
            return ResponseFormatter::success($get, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
