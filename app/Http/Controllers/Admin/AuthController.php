<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string|unique:admins',
            'email' => 'required|string|unique:admins',
            'password' => 'required|confirmed',
        ]);

        try {
            $user = new Admin();
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            $user->password = app('hash')->make($request->input('password'));
            $user->save();

            return response()->json([
                'entity' => 'admins',
                'action' => 'create',
                'result' => 'success'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'entity' => 'admins',
                'action' => 'create',
                'result' => 'failed'
            ], 409);
        }
    }

    public function login(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::guard('admins')->attempt($credentials)) {
            return response()->json(['errors' => 'The login detail is incorrect', 'message' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }

    public function profile()
    {
        return response()->json(['user' => auth::guard('admins')->user()]);
    }

    public function userList()
    {
        return User::where('is_verified', 1)->get();
    }

    public function userVerify($id)
    {
        $user = User::findOrFail($id);
        $user->status = 2;
        $user->save();
        return User::where('is_verified', 1)->get();
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admins')->factory()->getTTL()
        ], 200);
    }

}
