<?php

namespace App\Http\Controllers;

use App\Mail\AccountCreatedNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Http\Parser\AuthHeaders;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'pic' => 'nullable|string',
            'birth_date' => 'required|date|before_or_equal:today',
            'role' => 'nullable|in:super_admin,admin,user,writer',
        ]);

        // Assign default role as 'user' if not provided
        $role = $validatedData['role'] ?? 'user';

        // Create new user in the database
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'pic' => $validatedData['pic'] ?? null,
            'password' => Hash::make($validatedData['password']),
            'birth_date' => $validatedData['birth_date'],
            'role' => $role,
        ]);

        // Send welcome email if role is not 'user'
        if ($role !== 'user') {
            Mail::to($user->email)->send(new AccountCreatedNotification($user, $validatedData['password']));
        }

        // Return success response with user data
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }


    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'Invalid Credentials!'
            ], \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
        }
        $user = Auth::user();
        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 60 * 24); // 1 day
        return Response([
            "message" => $token,
            "role" => $user->role
        ])->withCookie($cookie);

    }

    public function user()
    {
        return Auth::user()->load(['requests', 'requests.requestFiles']);
    }

    public function logout()
    {
        $cookie = \Illuminate\Support\Facades\Cookie::forget('jwt');
        return response([
            "message" => "Logged out!"
        ])->withCookie($cookie);
    }

    public function deleteUser($id)
    {
        // Only super_admin can delete users
        if (Auth::user()->role !== 'super_admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    function getCommentsById(Request $request)
    {
        $user = Auth::user();
        return User::find($user["id"])->comments;
    }

    public function getWritersAndAdmins()
    {
        $users = User::whereIn('role', ['writer', 'admin'])->get();

        return response()->json([
            'users' => $users
        ]);
    }

    public function adminResetUserPassword(Request $request, $id)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);

        if (Auth::user()->role !== 'super_admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json(['message' => 'Password reset successfully']);
    }

    public function modifyUserRole(Request $request, $id)
    {
        // Only super_admin can change roles
        if (Auth::user()->role !== 'super_admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'role' => 'required|in:super_admin,admin,user,writer'
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->role = $validated['role'];
        $user->save();

        return response()->json(['message' => 'User role updated successfully']);
    }


}
