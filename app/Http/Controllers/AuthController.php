<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Validator;

class AuthController extends Controller
{
    public $successStatus = 200;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'fail', 'data' => $validator->errors()], 400);
        }

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            $error = "Unauthorized";
            return response()->json(['status' => 'error', 'message' => $error], 401);
        }
        $user = $request->user();

        if(!strlen($user->external_id) <= 0 || !$user->external_id == null) {
            $error = "External login";
            return response()->json(['status' => 'external', 'message' => $error], 401);
        }

        $roles = $user->roles->pluck('name');

        $success['token'] =  $user->createToken('token')->accessToken;
        $success['user'] = ['id'=> $user->id, 'name' => $user->name, 'roles' => $roles];
        return response()->json(['status' => 'success', 'data' => $success], 200);
    }

    public function signup(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $check_email_exists = User::where('email', $request->input('email'))->exists();
        if($check_email_exists) {
            return response()->json(['status' => 'error', 'message' => "This email is already taken."], 400);
        } else {
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            return response()->json(['status' => 'success', 'data' => $user], $this->successStatus);
        }
    }

    public function logout(Request $request)
    {

        $isUser = $request->user()->token()->revoke();
        if ($isUser) {
            $message = "Successfully logged out.";
            return response()->json(['status' => 'success', 'message' => $message], 200);
        } else {
            $error = "Something went wrong.";
            return response()->json(['status' => 'error', 'message' => $error], 401);
        }
    }
}
