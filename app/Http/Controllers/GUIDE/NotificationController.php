<?php

namespace App\Http\Controllers\GUIDE;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function index(Request $request)
    {

        $rules = [
            'guide_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');


        $query = Notification::where('guide_id', $request->guide_id)
                        ->where('is_open', 0)->count();

        if($query){
            $data = [
                'count' => $query,
            ];
            return ResponseFormatter::success($data, 'success');
        }else{
            $data = [
                'count' => $query,
            ];
            return ResponseFormatter::error($data, 'success');
        }

    }
}
