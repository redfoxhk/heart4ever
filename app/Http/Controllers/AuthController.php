<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'string',
                'min:12',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/'
            ],
            'nickname' => 'nullable|string|max:255',
            'real_name' => 'required|string|max:255',
            'id_card_number' => 'required|string|size:10|unique:users',
            'phone' => 'required|string|unique:users',
            'gender' => 'nullable|in:male,female,other',
            'birthdate' => 'required|date|before_or_equal:2014-12-31'
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nickname' => $request->nickname,
            'real_name' => $request->real_name,
            'id_card_number' => $request->id_card_number,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate
        ]);

        return response(['user' => $user], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response(['token' => $token], 200);
        }

        return response(['message' => 'Invalid credentials'], 401);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // 處理忘記密碼的邏輯，可以發送電子郵件連結給用戶
        return response(['message' => 'Password reset link sent to your email.'], 200);
    }

    public function getUser(Request $request)
    {
        return response(['user' => $request->user()], 200);
    }
}
