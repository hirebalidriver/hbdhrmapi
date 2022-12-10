<?php

namespace App\Http\Controllers\GENERAL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public static function upload($image, $filepath, $title)
    {
        $do = Storage::disk('s3');
        $title = SlugFormatterController::SlugFormatter($title);

        $file_name = 'ubudtrips_'. $title . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

        if(App::environment(['local', 'staging'])){
            $url = '/hirebalidriver/staging';
        }else{
            $url = '/hirebalidriver/prod';
        }

        $path = $url . '/' . $filepath. '/';

        $image->storeAs($path, $file_name);

        return $path.$file_name;
    }
}
