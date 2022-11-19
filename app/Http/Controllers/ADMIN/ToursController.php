<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Tours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ToursController extends Controller
{

    public function index(Request $request)
    {

        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $sortBy = $request->sortBy == null ? $sortBy = 'id' : $sortBy = $request->sortBy;
        $direction = $request->direction!= null ? 'DESC' : 'ASC';

        $tour = Tours::orderBy($sortBy, $direction)
                            ->paginate($per_page, ['*'], 'page', $page);

        if($tour){
            return ResponseFormatter::success($tour, 'success');
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

        $create = Tours::create([
            'title' => $request->title,
            'itinerary' => $request->itinerary,
            'guide_fee' => $request->guide_fee,
            'status' => $request->status,
            'note' => $request->note,
            'inclusions' => $request->inclusions,
            'exclusions' => $request->exclusions,
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
            'title' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $tour = Tours::find($request->id);

        if(!$tour) return ResponseFormatter::error(null, 'tour not found');

        $tour->title = $request->title;
        $tour->itinerary = $request->itinerary;
        $tour->guide_fee = $request->guide_fee;
        $tour->status = $request->status;
        $tour->note = $request->note;
        $tour->inclusions = $request->inclusions;
        $tour->exclusions = $request->exclusions;


        if($tour->save()) {
            return ResponseFormatter::success($tour, 'success');
        }else{
            return ResponseFormatter::error($tour, 'failed');
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

        $tour = Tours::find($request->id);

        if($tour) {
            return ResponseFormatter::success($tour, 'success');
        }else{
            return ResponseFormatter::error($tour, 'failed');
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

        $del = Tours::where('id', $request->id)->delete();

        if($del) {
            return ResponseFormatter::success($del, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
