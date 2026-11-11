<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Customer account API. Uses the browser session through Sanctum
 * (statefulApi), so the same login works for the cart and order API.
 */
class AuthController extends Controller
{
    /**
     * POST /api/tz/register
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'regex:/^[A-Za-z0-9._-]+$/', 'min:3', 'max:50', 'unique:users,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $customer = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'customer',
            'status' => 'active',
        ]);

        Auth::guard('web')->login($customer);
        $request->session()->regenerate();
        $request->session()->flash('success', 'Your TZ Carshop account is ready. Welcome, '.$customer->name.'!');

        return response()->json([
            'message' => 'Account created.',
            'user' => $this->format($customer),
            'redirect' => route('furever.shop'),
        ], 201);
    }

    /**
     * POST /api/tz/login
     * Accepts a username or an email address.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = User::query()
            ->where('username', $credentials['login'])
            ->orWhere('email', $credentials['login'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => 'The supplied username or password is incorrect.',
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'login' => 'This TZ Carshop account is inactive. Please contact the administrator.',
            ]);
        }

        Auth::guard('web')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->flash('success', 'Welcome back, '.$user->name.'!');

        return response()->json([
            'message' => 'Signed in.',
            'user' => $this->format($user),
            'redirect' => route($user->isFureverAdmin() ? 'furever.dashboard' : 'furever.shop'),
        ]);
    }

    /**
     * POST /api/tz/logout
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flash('success', 'You have been signed out.');

        return response()->json([
            'message' => 'Signed out.',
            'redirect' => route('furever.login'),
        ]);
    }

    /**
     * GET /api/tz/user
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json(['user' => $this->format($request->user())]);
    }

    private function format(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
        ];
    }
}
