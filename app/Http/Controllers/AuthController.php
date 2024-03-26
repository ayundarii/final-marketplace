<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('remember_token')->plainTextToken;

        return response()->json([
            'message' => "User succesfully created",
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    }

    public function login(Request $request)
    {
       $validator = Validator::make($request->all(), [
        'name' => 'required',
        'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => [
                    'errors' => $validator->invalid()
                ]
            ], 422);
        }

        $user = User::where('name', $request->name)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'name' => ['The provided credentials are incorrect.'],
        ]);
        }

        $token = $user->createToken("remember_token")->plainTextToken;

        return response()->json([
            'message' => 'User succesfully logged in',
            'access_token' => $token
        ], 200);
    }
}
