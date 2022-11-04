<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Packages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $rules = [
            'pages' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $pages = $request->pages != null ? $request->pages : 10;
        $sortBy = $request->sortby == null ? $sortBy = 'id' : $sortBy = $request->sortby;
        $direction = $request->direction!= null ? 'DESC' : 'ASC';

        // $packages = DB::table('packages')->select('packages.*')
        //                 ->join('package_relations', 'package_relations.package_id', 'packages.id')
        //                 ->join('tours', 'tours.id', 'package_relations.tour_id')
        //                 ->where('packages.id', '=', $re)

        $packages = Packages::orderBy($sortBy, $direction)
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
            'title' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $create = Packages::create([
            'title' => $request->title,
            'note' => $request->note,
            'price_guide' => $request->price_guide,
            'status' => $request->status,
        ]);

        if($create) {
            return ResponseFormatter::success($create, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $update = Packages::find($request->id);
        $update->title = $request->title;
        $update->note = $request->note;
        $update->price_guide = $request->price_guide;
        $update->status = $request->status;


        if($update->save()) {
            return ResponseFormatter::success($update, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }


    public function find(Request $request)
    {
        $rules = [
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $find = Packages::find($request->id);

        if($find) {
            return ResponseFormatter::success($find, 'success');
        }else{
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

        $delete = Packages::where('id',$request->id)->delete();

        if($delete) {
            return ResponseFormatter::success($delete, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }



}
