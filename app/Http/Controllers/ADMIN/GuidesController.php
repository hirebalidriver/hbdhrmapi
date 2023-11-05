<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Balances;
use App\Models\Guides;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class GuidesController extends Controller
{
    public function index(Request $request)
    {
        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $sortBy = $request->sortBy == null ? $sortBy = 'id' : $sortBy = $request->sortBy;
        $direction =$request->input('direction', 'DESC');

        $guide = Guides::orderBy($sortBy, $direction)
                            ->paginate($per_page, ['*'], 'page', $page);

        if($guide){
            return ResponseFormatter::success($guide, 'success');
        }else{
            return ResponseFormatter::error(null, 'success');
        }
    }

    public function guideAvailability(Request $request)
    {

        $rules = [
            'date' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $date = date('Y-m-d', strtotime($request->date));;

        $ava = DB::table('availabilities')->whereDate('date', $date)->pluck('guide_id')->toArray();

        $guides = Guides::whereNotIn('id',$ava)->get();

        if($guides){
            return ResponseFormatter::success($guides, 'success');
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

        DB::beginTransaction();

        try{
            $guide = Guides::insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'ktp_number' => $request->ktp_number,
                'ktp_url' => $request->ktp_url,
                'code' => $request->code,
                'address' => $request->address,
                'status' => $request->status,
            ]);

            Balances::create([
                'guide_id' => $guide,
                'trx_id' => 0,
                'in' => 0,
                'out' => 0,
                'type' => 'begin',
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

        $find = Guides::find($request->id);

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
            'name' => ['required'],
            'email' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        DB::beginTransaction();

        try{

            $guide = Guides::where('id', $request->id)->first();
            $guide->name = $request->name;
            $guide->email = $request->email;
            $guide->phone = $request->phone;
            $guide->ktp_number = $request->ktp_number;
            $guide->ktp_url = $request->ktp_url;
            $guide->code = $request->code;
            $guide->address = $request->address;
            $guide->status = $request->status;

            if($request->password != null or $request->password != ""){
                $guide->password = Hash::make($request->password);
            }

            $guide->save();

            $balance = Balances::where('guide_id', $guide->id)->first();
            if(!$balance) {
                Balances::create([
                    'guide_id' => $guide->id,
                    'trx_id' => 0,
                    'in' => 0,
                    'out' => 0,
                    'type' => 'begin',
                ]);
            }

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

        $del = Guides::where('id', $request->id)->delete();

        if($del) {
            return ResponseFormatter::success($del, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
