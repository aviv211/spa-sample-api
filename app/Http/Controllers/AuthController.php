<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Validator;
use DB;
use Carbon\Carbon;
use Mail;

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

    public function forgot(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);
    
        $token = $this->generateRandomString(64);
    
        DB::table('password_resets')->insert(
            ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now()]
        );

        Mail::send('email.forgot', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password Notification');
        });

        return response()->json(["message" => 'Reset password link sent on your email address.']);
    }

    public function reset(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $updatePassword = DB::table('password_resets')
            ->where(['email' => $request->email, 'token' => $request->token])
            ->first();

            if(!$updatePassword)
            return back()->withInput()->with('error', 'Invalid token!');
      
        $user = User::where('email', $request->email)
                    ->update(['password' => bcrypt($request->password)]);
    
        DB::table('password_resets')->where(['email'=> $request->email])->delete();

        return response()->json(["message" => "Password has been successfully changed."]);
    }

    public function generateRandomString($length = 64) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
