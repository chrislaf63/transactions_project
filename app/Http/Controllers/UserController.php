<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User;

class UserController extends Controller
{
    use HasApiTokens;

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate user and generate JWT token",
     *     description="Login",
     *     operationId="login",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *     required=true,
     *     description="Pass login details",
     *     @OA\JsonContent(
     *     required={"email","password"},
     *     @OA\Property(property="email", type="string", format="email", example="test@example.com"),
     *     @OA\Property(property="password", type="string", format="password", example="password")
     *   )
     * ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     *     @OA\Response(response=422, description="Unprocessable Entity")
     * )
     */


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

    /**
     * @OA\Post(
     * path="/api/register",
     * summary="Register",
     * description="Register",
     * operationId="register",
     * tags={"Users"},
     * @OA\RequestBody(
     * required=true,
     * description="Pass registration details",
     * @OA\JsonContent(
     * required={"name","email","password","password_confirmation"},
     * @OA\Property(property="name", type="string", example="Chris"),
     * @OA\Property(property="email", type="string", format="email", example="test@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="password")
     * )
     * ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     *     @OA\Response(response=422, description="Unprocessable Entity")
     * )
     */

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

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get all users",
     *     description="Get all users",
     *     operationId="getAllUsers",
     *     tags={"Users"},
     *     security={ {"sanctum": {} }},
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     *     @OA\Response(response=422, description="Unprocessable Entity")
     * )
     */

    public function index()
    {
        $userId = Auth::id();
        $loggedUser = User::find($userId);
        if($loggedUser->role != 'admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $users = User::all();
        return $users;
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get a user",
     *     description="Get a user",
     *     operationId="getUserById",
     *     tags={"Users"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of user to return",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     *    )
     *  ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     *     @OA\Response(response=422, description="Unprocessable Entity")
     * )
     */
    public function show($id)
    {
        $user = User::find($id);
        return $user;
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Update a user",
     *     description="Update a user",
     *     operationId="updateUsezrById",
     *     tags={"Users"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of user to update",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     *    )
     *  ),
     *     @OA\RequestBody(
     *     required=true,
     *     description="Pass updated details",
     *     @OA\JsonContent(
     *     required={"name","email","role"},
     *     @OA\Property(property="name", type="string", example="Chris"),
     *     @OA\Property(property="email", type="string", format="email", example="test@example.com"),
     *     @OA\Property(property="role", type="string", example="admin")
     *    )
     * ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     *     @OA\Response(response=422, description="Unprocessable Entity")
     * )
     */

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $userId = Auth::id();
        $loggedUser = User::find($userId);
        if($loggedUser->role != 'admin' && $loggedUser->id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user->update($request->all());
        return response()->json(['message' => 'User updated',
            'user' => $user,
            ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete a user",
     *     description="Delete a user",
     *     operationId="destroyUserById",
     *     tags={"Users"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of user to delete",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     *    )
     *  ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     *     @OA\Response(response=422, description="Unprocessable Entity")
     * )
     */

    public function destroy($id)
    {
        $user = User::find($id);
        $userId = Auth::id();
        $loggedUser = User::find($userId);
        if($loggedUser->role != 'admin' && $loggedUser->id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted'], 200);
    }
}
