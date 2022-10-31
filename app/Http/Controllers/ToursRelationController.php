<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\ExclusionRelations;
use App\Models\InclusionRelations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ToursRelationController extends Controller
{

    public function inclusions(Request $request)
    {
        $inc = DB::table('inclusions')
                    ->join('inclusion_relations', 'inclusion_relations.inclusion_id', 'inclusions.id')
                    ->where('inclusion_relations.tour_id', $request->tour_id)
                    ->get();

        if ($inc) {
            return ResponseFormatter::success($inc, 'success');
        } else {
            return ResponseFormatter::error(null, 'failed');
        }

    }

    public function exclusions(Request $request)
    {
        $exc = DB::table('exclusions')
                    ->join('exclusion_relations', 'exclusion_relations.exclusion_id', 'exclusions.id')
                    ->where('exclusion_relations.tour_id', $request->tour_id)
                    ->get();

        if ($exc) {
            return ResponseFormatter::success($exc, 'success');
        } else {
            return ResponseFormatter::error(null, 'failed');
        }

    }

    public function addInclusion(Request $request)
    {
        $rules = [
            'tour_id' => ['required'],
            'inclusion_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $create = InclusionRelations::create([
            'tour_id' => $request->tour_id,
            'inclusion_id' => $request->inclusion_id,
        ]);

        if($create) {
            return ResponseFormatter::success($create, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function delInclusion(Request $request)
    {
        $rules = [
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $del = InclusionRelations::where('id', $request->id)->delete();

        if($del) {
            return ResponseFormatter::success($del, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }


    // EXCLUSION ======
    public function addExclusion(Request $request)
    {
        $rules = [
            'tour_id' => ['required'],
            'exclusion_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $create = ExclusionRelations::create([
            'tour_id' => $request->tour_id,
            'exclusion_id' => $request->exclusion_id,
        ]);

        if($create) {
            return ResponseFormatter::success($create, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function delExclusion(Request $request)
    {
        $rules = [
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $del = ExclusionRelations::where('id', $request->id)->delete();

        if($del) {
            return ResponseFormatter::success($del, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
