<?php

namespace App\Http\Controllers\Guide;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GENERAL\ImageUploadController;
use App\Http\Resources\GuideResource;
use App\Models\Balances;
use App\Models\Guides;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        $user = Guides::where('email', $request->email)->first();
        if (!Hash::check($request->password, $user->password, [])) {
            throw new \Exception('Invalid Credentials');
        }

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        if ($tokenResult) {
            return response()->json(['access_token' => $tokenResult, 'user'=>$user], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'max:50'],
        ]);


        DB::beginTransaction();
        try {

            $guide = Guides::insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Balances::create([
                'guide_id' => $guide,
                'trx_id' => 0,
                'in' => 0,
                'out' => 0,
                'type' => 'begin',
            ]);

            $user = Guides::where('id', $guide)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            DB::commit();

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $guide
            ], 'User Registered');

        } catch (Exception $error) {
            DB::rollBack();
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication Failed', 404);
        }
    }

    public function me()
    {
        $query = auth()->guard('guide')->user();

        if($query) {
            return ResponseFormatter::success(new GuideResource($query), 'success');
        }

        return ResponseFormatter::error(null, 'failed');
    }

    public function reset(Request $request)
    {
        $rules = [
            'email' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');

        $user = Guides::where('email', $request->email)->first();

        if (!$user) {
            return ResponseFormatter::error(null, 'Email belum terdaftar');
        }

        // SEND EMAIL
        $otp = random_int(100000, 999999);

        $user->code = strval($otp);

        $details = [
            'title' => 'Atur Ulang Kata Sandi Anda',
            'to' => $user->email,
            'otp' => $otp,
            'name' => $user->name,
        ];

        if($user->save()){
            \App\Jobs\ResetGuideMailJob::dispatch($details);

            return ResponseFormatter::success(null, 'Reset password');
        }else{
            return ResponseFormatter::error(null, 'please try again');
        }

    }

    public function checkOTP(Request $request)
    {
        $rules = [
            'otp' => ['required', 'max:255']
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');

        $query = Guides::where('code', $request->otp)->first();

        if ($query) {
            return ResponseFormatter::success(null, 'success');
        } else {
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function changePassword(Request $request)
    {
        $rules = [
            'otp' => ['required', 'max:255'],
            'password' => ['required', 'confirmed', 'max:255'],
            'password_confirmation' => ['required', 'max:255'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');

        $user = Guides::where('code', $request->otp)->first();
        if (!$user) return ResponseFormatter::error(null, 'otp failed');

        $user->password = Hash::make($request->password);

        if ($user->save()) {
            return ResponseFormatter::success(null, 'success');
        } else {
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function updateProfile(Request $request)
    {
        $rules = [
            'name' => ['required', 'max:255'],
            'email' => ['required', 'max:255'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');

        $user = auth()->guard('guide')->user();
        if (!$user) return ResponseFormatter::error(null, 'not found user');

        if($request->ktp_url != null || $request->ktp_url != ''){
            $uploadKTP = ImageUploadController::upload($request->ktp_url, $user->id, 'ktp');
        }

        if($request->profile != null || $request->profile != ''){
            $uploadProfile = ImageUploadController::upload($request->profile, $user->id, 'profile');
        }

        if($request->car_photo != null || $request->car_photo != ''){
            $uploadCar = ImageUploadController::upload($request->car_photo, $user->id, 'car');
        }

        $phone = (int)$request->phone;
        $phone = (string)$phone;

        $query = Guides::find($user->id);
        $query->name = $request->name;
        $query->email = $request->email;
        $query->phone = $phone;
        $query->ktp_number = $request->ktp_number;
        $query->ktp_url = $uploadKTP;
        $query->profile = $uploadProfile;
        $query->car_photo = $uploadCar;
        $query->car_type = $request->car_type;
        $query->plat_number = $request->plat_number;
        $query->car_color = $request->car_color;
        $query->address = $request->address;

        if ($query->save()) {
            return ResponseFormatter::success(null, 'success');
        } else {
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
