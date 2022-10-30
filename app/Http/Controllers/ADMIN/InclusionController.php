<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Inclusions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InclusionController extends Controller
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

        $inc = Inclusions::orderBy($sortBy, $direction)
                            ->paginate($pages);

        if($inc){
            return ResponseFormatter::success($inc, 'success');
        }else{
            return ResponseFormatter::error(null, 'success');
        }
    }
}
