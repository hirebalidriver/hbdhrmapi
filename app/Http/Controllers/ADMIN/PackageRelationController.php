<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\PackageRelations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageRelationController extends Controller
{
    public function index(Request $request)
    {
        $rules = [
            'package_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $pages = $request->pages != null ? $request->pages : 10;
        $sortBy = $request->sortby == null ? $sortBy = 'id' : $sortBy = $request->sortby;
        $direction = $request->direction!= null ? 'DESC' : 'ASC';

        $packages = PackageRelations::where('package_id', $request->package_id)
                                    ->orderBy($sortBy, $direction)
                                    ->paginate($pages);

        if($packages){
            return ResponseFormatter::success($packages, 'success');
        }else{
            return ResponseFormatter::error(null, 'success');
        }
    }

    public function add(Request $request)
    {
        $rules = [
            'package_id' => ['required'],
            'tour_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $create = PackageRelations::create([
            'package_id' => $request->package_id,
            'tour_id' => $request->tour_id,
        ]);

        if($create) {
            return ResponseFormatter::success($create, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function delete(Request $request)
    {
        $rules = [
            'package_id' => ['required'],
            'tour_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $delete = PackageRelations::where('package_id', $request->package_id)
                                    ->where('tour_id', $request->tour_id,)
                                    ->delete();

        if($delete) {
            return ResponseFormatter::success($delete, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
