<?php

namespace App\Http\Controllers\GUIDE;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function index()
    {

        $user = auth()->guard('guide')->user();

        $query = Notification::where('guide_id', $user->id)
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
