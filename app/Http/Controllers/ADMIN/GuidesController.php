<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Guides;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuidesController extends Controller
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

        $guide = Guides::orderBy($sortBy, $direction)
                            ->paginate($pages);

        if($guide){
            return ResponseFormatter::success($guide, 'success');
        }else{
            return ResponseFormatter::error(null, 'success');
        }
    }

    public function add(Request $request)
    {
        $rules = [
            'name' => ['required'],
            'email' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $create = Guides::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'ktp_number' => $request->ktp_number,
            'ktp_url' => $request->ktp_url,
            'code' => $request->code,
            'address' => $request->address,
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
            'name' => ['required'],
            'email' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $update = Guides::where('id', $request->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'ktp_number' => $request->ktp_number,
            'ktp_url' => $request->ktp_url,
            'code' => $request->code,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        if($update) {
            return ResponseFormatter::success($update, 'success');
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

        $del = Guides::where('id', $request->id)->delete();

        if($del) {
            return ResponseFormatter::success($del, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
