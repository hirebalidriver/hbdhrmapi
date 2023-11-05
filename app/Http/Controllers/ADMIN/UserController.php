<?php

namespace App\Http\Controllers\ADMIN;

use App\Http\Controllers\Controller;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $sortBy = $request->sortBy == null ? $sortBy = 'id' : $sortBy = $request->sortBy;
        $direction =$request->input('direction', 'DESC');

        $users = User::orderBy($sortBy, $direction)
                            ->paginate($per_page, ['*'], 'page', $page);

        if($users){
            return ResponseFormatter::success($users, 'success');
        }else{
            return ResponseFormatter::error(null, 'success');
        }
    }


    public function add(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'max:50'],
                'role' => ['required', 'string'],
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($request->password),
            ]);

            DB::commit();

            return ResponseFormatter::success(null, 'User Registered');

        } catch (Exception $error) {
            DB::rollBack();
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication Failed', 404);
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'id' => ['required'],
            ]);

            $user = User::where('id', $request->id)->first();
            
            
            $user->name = $request->name;
            $user->email = $request->email;
            if($request->password != null or $request->password != ""){
                $user->password = Hash::make($request->password);
            }
            $user->role = $request->role;

            $user->save();
        

            DB::commit();

            return ResponseFormatter::success(null, 'User Updated');

        } catch (Exception $error) {
            DB::rollBack();
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication Failed', 404);
        }
    }

    public function find(Request $request)
    {
   
            $request->validate([
                'id' => ['required'],
            ]);

            $user = User::where('id', $request->id)->first();
            if($user) {
                return ResponseFormatter::success($user, 'User find');
            }else{
                return ResponseFormatter::error([
                    'message' => 'Something went wrong',
                    'error' => $error,
                ], 'Authentication Failed', 404);
            }
            
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'id' => ['required'],
            ]);

            User::where('id', $request->id)->delete();
        

            DB::commit();

            return ResponseFormatter::success(null, 'User Deleted');

        } catch (Exception $error) {
            DB::rollBack();
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication Failed', 404);
        }
    }
}
