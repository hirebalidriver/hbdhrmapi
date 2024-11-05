<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Balances;
use App\Models\BlackPeriod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BlackPeriodController extends Controller
{
    public function index(Request $request)
    {
        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $sortBy = $request->sortBy == null ? $sortBy = 'id' : $sortBy = $request->sortBy;
        $direction =$request->input('direction', 'DESC');

        $black_period = BlackPeriod::orderBy($sortBy, $direction)
                            ->paginate($per_page, ['*'], 'page', $page);

        if($black_period){
            return ResponseFormatter::success($black_period, 'success');
        }else{
            return ResponseFormatter::error(null, 'success');
        }
    }


    public function add(Request $request)
    {
        $rules = [
            'date' => ['required'],
            'description' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        DB::beginTransaction();

        try{
            $black_period = BlackPeriod::create([
                'date' => $request->date,
                'description' => $request->description,
            ]);

            DB::commit();

            return ResponseFormatter::success(null, 'success');

        }catch(Exception) {
            DB::rollback();
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

        $find = BlackPeriod::find($request->id);

        if($find) {
            return ResponseFormatter::success($find, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => ['required'],
            'date' => ['required'],
            'description' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        DB::beginTransaction();

        try{

            $black_period = BlackPeriod::where('id', $request->id)->first();
            $black_period->date = $request->date;
            $black_period->description = $request->description;

            $black_period->save();

            DB::commit();

            return ResponseFormatter::success(null, 'success');

        }catch(Exception) {
            DB::rollBack();
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

        $del = BlackPeriod::where('id', $request->id)->delete();

        if($del) {
            return ResponseFormatter::success($del, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
