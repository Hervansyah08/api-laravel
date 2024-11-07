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
            ], 401);
        }
        $data =  $request->all();
        // bcrypt ini lebih bagus dari pada hash, dan ini khusus untuk password
        $data['password'] =  bcrypt($request->password);

        $user = User::create($data);

        $at_expiration =  60;
        // ini mebuat token dengan nama acces_token, dan memiliki ability acces-api dan waktu expired e 1 jam
        $acces_token = $user->createToken('acces_token', ['acces-api'], Carbon::now()->addMinutes($at_expiration))->plainTextToken;

        // ini mebuat token refresh_token, dan memiliki ability issue-acces-token dan waktu expired e 30 hari/ 1 bulan
        $rt_expiration =  30 * 24 * 60;
        $refresh_token = $user->createToken('refresh_token', ['issue-acces-token'], Carbon::now()->addMinutes($rt_expiration))->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User berhasil didaftarkan',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'token' => $acces_token,
                'refresh_token' => $refresh_token,
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
            ], 401);
        }
        // attempt() adalah metode yang digunakan untuk mencoba melakukan autentikasi dengan kredensial yang diberikan.
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->first();

            $at_expiration =  60;
            // ini mebuat token dengan nama acces_token, dan memiliki ability acces-api dan waktu expired e 1 jam
            $acces_token = $user->createToken('acces_token', ['acces-api'], Carbon::now()->addMinutes($at_expiration))->plainTextToken;

            // ini mebuat token refresh_token, dan memiliki ability issue-acces-token dan waktu expired e 30 hari/ 1 bulan
            $rt_expiration =  30 * 24 * 60;
            $refresh_token = $user->createToken('refresh_token', ['issue-acces-token'], Carbon::now()->addMinutes($rt_expiration))->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'berhasil login',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $acces_token,
                    'refresh_token' => $refresh_token,
                ],
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'authentication failed',
            ], 401);
        }
    }

    public function refreshToken(Request $request)
    {
        $at_expiration =  60;
        // ini mebuat token dengan nama acces_token, dan memiliki ability acces-api dan waktu expired e 1 jam
        $acces_token = $request->user()->createToken('acces_token', ['acces-api'], Carbon::now()->addMinutes($at_expiration))->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'token berhasil diperbarui',
            'data' => [
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'token' => $acces_token,
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Berhasil logout'
        ], 200);
    }
}
