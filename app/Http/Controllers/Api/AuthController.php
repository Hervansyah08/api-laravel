<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $data =  $request->all();
        // bcrypt ini lebih bagus dari pada hash, dan ini khusus untuk password
        $data['password'] =  bcrypt($request->password);

        $user = User::create($data);
        $token = $user->createToken('my-api')->plainTextToken;

        // ini membuat expired token, bisa di cek di database di tabel personal_access_tokens
        $personalAccessToken = $user->tokens()->latest()->first();
        $personalAccessToken->expires_at = Carbon::now()->addHours();
        $personalAccessToken->save();

        return response()->json([
            'status' => true,
            'message' => 'User berhasil didaftarkan',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'token' => $token,
            ],
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi error',
                'errors' => $validator->errors(),
            ], 422);
        }
        // attempt() adalah metode yang digunakan untuk mencoba melakukan autentikasi dengan kredensial yang diberikan.
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('my-api')->plainTextToken;

            // ini membuat expired token, bisa di cek di database di tabel personal_access_tokens
            $personalAccessToken = $user->tokens()->latest()->first();
            $personalAccessToken->expires_at = Carbon::now()->addHours();
            $personalAccessToken->save();

            return response()->json([
                'status' => true,
                'message' => 'berhasil login',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $token,
                ],
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'authentication failed',
            ], 401);
        }
    }
}
