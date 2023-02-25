<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Availability;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {

        $start = Carbon::parse($request->date);
        $end = Carbon::parse($request->date)->addDays(30);


         $query = Availability::when($start, function($query) use ($start, $end){
                                return $query->whereBetween('date', [$start, $end]);
                            })->with('booking', 'guide')->get();

        if($query) {
            return ResponseFormatter::success($query, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function addDayOff(Request $request)
    {
        $rules = [
            'date' => ['required'],
            'guide_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        DB::beginTransaction();

        try{

            if($request->date_end){

                $start = Carbon::parse($request->date);
                $end = Carbon::parse($request->date_end);

                $days = $start->diffInDays($end);
                // return ResponseFormatter::success($days, 'success');

                // $data= [];
                $tgl = [];
                for($x=0;$x<=$days;$x++) {
                    $tgl[$x] = Carbon::parse($request->date);
                    $add = $tgl[$x]->addDays($x);

                    $check = Availability::where('date', $add)
                                    ->where('guide_id', $request->guide_id)->first();

                    if(!$check){

                        Availability::create([
                            'guide_id' => $request->guide_id,
                            'date' => Carbon::parse($add),
                            'note' => $request->note,
                        ]);
                    }

                }
            }

            DB::commit();

            return ResponseFormatter::success(null, 'success');


        }catch(Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error(null, 'failed');
        }

    }

    public function removeDayOff(Request $request)
    {
        $rules = [
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        DB::beginTransaction();

        try{

            Availability::where('id', $request->id)->delete();

            DB::commit();

            return ResponseFormatter::success(null, 'success');


        }catch(Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error(null, 'failed');
        }

    }
}
