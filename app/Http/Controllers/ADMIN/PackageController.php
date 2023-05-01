<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GENERAL\ImageUploadController;
use App\Models\Packages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $sortBy = $request->sortBy == null ? $sortBy = 'id' : $sortBy = $request->sortBy;
        $direction =$request->input('direction', 'DESC');

        $packages = Packages::orderBy($sortBy, $direction)
                        ->paginate($per_page, ['*'], 'page', $page);

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
            'status' => $request->status,
            'tour_code' => $request->tour_code,
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

        if($request->thumb) {
            $thumb = ImageUploadController::upload($request->thumb, 'web', 'package');
            $update->thumb = $thumb;
        }

        $update->title = $request->title;

        $update->note = $request->note;
        $update->status = $request->status;
        $update->tour_code = $request->tour_code;


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
