<?php

namespace App\Http\Controllers\GUIDE;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Availability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {

        $user = auth()->guard('guide')->user();

        $per_page = $request->input('per_page', 50);
        $page = $request->input('page', 1);
        $sortBy = $request->sortBy == null ? $sortBy = 'id' : $sortBy = $request->sortBy;
        $direction =$request->input('direction', 'DESC');

        if($request->date_from > $request->date_end){
            $start = $request->date_end;
            $end = $request->date_from;
        }else{
            $start = $request->date_from;
            $end = $request->date_end;
        }

         $query = Availability::when($start, function($query) use ($start, $end){
                                return $query->whereBetween('date', [$start, $end]);
                            })
                            ->where('guide_id', $user->id)
                            ->orderBy($sortBy, $direction)
                            ->paginate($per_page, ['*'], 'page', $page);

        if($query) {
            return ResponseFormatter::success($query, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function add(Request $request)
    {

        $rules = [
            'date' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');

        $user = auth()->guard('guide')->user();

        $create = Availability::create([
            'guide_id' => $user->id,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'note' => $request->note,
        ]);


        if($create) {
            return ResponseFormatter::success($request->all(), 'success');
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
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');

        $user = auth()->guard('guide')->user();

        $query = Availability::where('id', $request->id)
                        ->where('guide_id', $user->id)->delete();


        if($query) {
            return ResponseFormatter::success(null, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
