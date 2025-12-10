<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use App\Mail\UserRegisteredAdminMail;
use App\Mail\UserRegisteredUserMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'company' => ['nullable', 'string', 'max:255'],
        ]);

        $token = Str::random(60);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => config('constants.USER', 1),
        ]);

        $user->remember_token = $token;
        $user->save();

        // Send notifications
        $adminEmail = config('mail.from.address') ?: env('MAIL_FROM_ADDRESS');
        try {
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new UserRegisteredAdminMail($user));
            }
            Mail::to($user->email)->send(new UserRegisteredUserMail($user));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 422);
        }

        $token = Str::random(60);
        $user->remember_token = $token;
        $user->save();

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return response()->json([
                'message' => 'Email not found in our database.',
            ], 404);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $resetBase = config('app.frontend_url') ?? config('app.url');
        $resetUrl = rtrim($resetBase, '/') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);

        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user, $resetUrl, $token));
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Could not send reset email. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }

        // In a real app we would email the token. For this build, return it so the SPA can use it directly.
        return response()->json([
            'success' => true,
            'message' => 'Password reset token created.',
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $validated['email'])->first();

        if (! $record) {
            return response()->json(['message' => 'Email or reset token is invalid or has expired.'], 422);
        }

        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->lt(now()->subMinutes(60))) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
            return response()->json(['message' => 'Email or reset token is invalid or has expired.'], 422);
        }

        if (! Hash::check($validated['token'], $record->token)) {
            return response()->json(['message' => 'Email or reset token is invalid or has expired.'], 422);
        }

        $user = User::where('email', $validated['email'])->first();
        if (! $user) {
            return response()->json(['message' => 'Email or reset token is invalid or has expired.'], 422);
        }

        $user->password = Hash::make($validated['password']);
        $user->remember_token = null;
        $user->save();

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset. Please log in with your new credentials.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->userFromRequest($request);
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json($user);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $this->userFromRequest($request);
        if ($user) {
            $user->remember_token = null;
            $user->save();
        }

        return response()->json(['success' => true]);
    }

    private function userFromRequest(Request $request): ?User
    {
        $authHeader = $request->header('Authorization');
        if (! $authHeader || ! str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = substr($authHeader, 7);
        if ($token === '') {
            return null;
        }

        return User::where('remember_token', $token)->first();
    }
}
