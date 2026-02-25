<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\ForgotPasswordAdminMail;
use App\Mail\PasswordChangedUserMail;
use App\Mail\ResetPasswordMail;
use App\Mail\UserRegisteredAdminMail;
use App\Mail\UserRegisteredUserMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
        ], [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'An account with this email already exists.',
            'password.required' => 'Please enter a password.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password and confirmation do not match.',
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
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
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
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
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
            $adminEmail = config('mail.from.address') ?: env('MAIL_FROM_ADDRESS');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new ForgotPasswordAdminMail($user));
            }
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Could not send reset email. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'If an account exists for that email, you will receive a password reset link shortly.',
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'token.required' => 'Reset token is missing.',
            'password.required' => 'Please enter a new password.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password and confirmation do not match.',
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

        try {
            Mail::to($user->email)->send(new PasswordChangedUserMail($user));
        } catch (\Throwable $e) {
            report($e);
        }

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

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $this->userFromRequest($request);
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = ['file', 'mimetypes:image/jpeg,image/pjpeg,image/png,image/gif,image/webp', 'max:2048'];
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'An account with this email already exists.',
            'image.mimetypes' => 'The file must be an image (JPEG, PNG, GIF or WebP).',
            'image.max' => 'The image may not be greater than 2MB.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->company = $validated['company'] ?? null;
        $user->address_line1 = $validated['address_line1'] ?? null;
        $user->address_line2 = $validated['address_line2'] ?? null;
        $user->city = $validated['city'] ?? null;
        $user->state = $validated['state'] ?? null;
        $user->postal_code = $validated['postal_code'] ?? null;
        $user->country = $validated['country'] ?? null;

        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $path = $request->file('image')->store('avatars', 'public');
            $user->image = $path;
        }

        $user->save();

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
