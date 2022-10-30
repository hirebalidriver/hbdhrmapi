<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Jobs\ResetMailJob;
use App\Models\User;
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

        $user = User::where('email', $request->email)->first();
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
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'max:50'],
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            DB::commit();

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Registered');

        } catch (Exception $error) {
            DB::rollBack();
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication Failed', 404);
        }
    }

    public function resetPassword(Request $request)
    {
        $rules = [
            'email' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ResponseFormatter::error(null, 'Email belum terdaftar');
        }

        // SEND EMAIL
        $otp = substr(md5(uniqid(mt_rand(), true)) , 0, 20);
        $link = "https://hrm.hirebalidriver.com/forgot/password/".$otp;

        $user->code = strval($otp);

        $details = [
            'title' => 'Atur Ulang Kata Sandi',
            'to' => $user->email,
            'link' => $link,
            'name' => $user->name,
        ];

        if($user->save()){
            \App\Jobs\ResetMailJob::dispatch($details);

            return ResponseFormatter::success($link, 'Reset password');
        }else{
            return ResponseFormatter::error(null, 'please try again');
        }


    }

    public function changePass(Request $request)
    {
        $rules = [
            'code' => ['required', 'max:255'],
            'password' => ['required', 'confirmed', 'max:255'],
            'password_confirmation' => ['required', 'max:255'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');

        $user = User::where('code', $request->code)->first();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->code = null;
            if($user->save()){
                return ResponseFormatter::success($user, 'Password success');
            }
        } else {
            return ResponseFormatter::error(null, 'Password failed');
        }
    }

    public function changeProfilePass(Request $request)
    {
        $rules = [
            'password' => ['required', 'confirmed', 'max:255'],
            'password_confirmation' => ['required', 'max:255'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Validation Failed');

        $user = Auth::user();
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            return ResponseFormatter::success($user, 'Password Berhasil Disimpan');
        } else {
            return ResponseFormatter::error(null, 'Password Gagal Disimpan');
        }
    }
}
