<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate( [
            'email' => ['required','email'],
            'password' => ['required']
        ]);

        if(Auth::attempt($credentials)) {
            if(auth('sanctum')->check()) {
            auth()->user()->tokens()->delete();
            }
            $user = User::where('email', $credentials['email'])->first();
            $token = $user->createToken('authToken', ['*'])
                ->plainTextToken;
            return response()->json(['message' => 'Login successful',
                'token' => $token,
                'user' => $user,
                ], 200);
        } else{
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required','max:255'],
            'email' => ['required','email','unique:users'],
            'password' => ['required'],
            'password_confirmation' => ['required','same:password'],
        ]);

        $credentials['password'] = bcrypt($credentials['password']);
        $user = User::create($credentials);
        $token = $user->createToken('authToken', ['*'])
            ->plainTextToken;
        return response()->json(['message' => 'Registration successful',
            'token' => $token,
            'user' => $user,
            ], 201);
    }

    public function index()
    {
        $users = User::all();
        return $users;
    }

    public function show($id)
    {
        $user = User::find($id);
        return $user;
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->update($request->all());
        return response()->json(['message' => 'User updated',
            'user' => $user,
            ], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(['message' => 'User deleted'], 200);
    }
}
