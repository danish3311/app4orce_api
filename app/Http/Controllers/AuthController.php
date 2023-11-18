<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{

    public function register(Request $request)
    {
        try {
            //validate the request

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:8',
            ]);

            //attempting to create user

            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            //return success response if user is created

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => $user,
            ], 201); // HTTP status code 201 for resource creation

        } catch (ValidationException $e) {

            //return validation error if there any
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422); // HTTP status code 422 for unprocessable entity (validation error)

        } catch (\Exception $e) {

            //return generic errors if there are any
            return response()->json([
                'success' => false,
                'message' => 'Error registering user',
                'error' => $e->getMessage(),
            ], 500); // HTTP status code 500 for internal server error


        }
    }



    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('auth-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'User logged in successfully',
                    'data' => [
                        'user' => $user,
                        'token' => $token,
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error logging in',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function logout(Request $request)
    {

        //revoking the token to logout the user
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully',
            ]);


        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error logging out',
                'error' => $e->getMessage(),
            ], 500); // HTTP status code 500 for internal server error

        }
    }



}
