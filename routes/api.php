<?php

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/categories', function () {
    return Category::has('products')->get();
});

// Register API - Use web middleware for session support
Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    // Log the user in using web guard (session-based)
    Auth::login($user);

    return response()->json([
        'message' => 'Registration successful',
        'user' => $user,
        'redirect' => '/dashboard'
    ], 201);
})->middleware('web');

// Login API - Use web middleware for session support
Route::post('/login', function (Request $request) {
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    // Attempt to authenticate the user
    if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login successful',
            'user' => Auth::user(),
            'redirect' => '/dashboard'
        ], 200);
    }

    throw ValidationException::withMessages([
        'email' => ['The provided credentials are incorrect.'],
    ]);
})->middleware('web');

// Logout API - Use web middleware for session support
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'message' => 'Logged out successfully'
    ], 200);
})->middleware(['web', 'auth']);
