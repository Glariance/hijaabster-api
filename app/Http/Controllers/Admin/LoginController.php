<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Admin\AdminResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }
    public function forgot()
    {
        return view('admin.auth.forgot');
    }
    public function forgotpost(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        try {
            $token = Str::random(60);
            $email = $request->email;
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]
            );
            $resetUrl = url('/admin/reset-password?token=' . $token . '&email=' . urlencode($email));
            $this->sendMailHelper($email, AdminResetPasswordMail::class, $resetUrl);

            return response()->json(['success' => 'Password reset link sent to your email.']);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
        //     return $this->forSuccessResponse('Password reset link sent to your email.');
        // } catch (\Exception $e) {
        //     return $this->forErrorResponse('Mail sending failed: ' . $e->getMessage(), 500);
        // }
    }
    public function resetPassword(Request $request)
    {
        return view('admin.auth.reset_password', ['token' => $request->token, 'email' => $request->email]);
    }
    public function resetPasswordPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:password_reset_tokens,email',
            'token' => 'required',
            'password' => 'required|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$record || $request->token != $record->token) {
            return response()->json(['errors' => 'Invalid or expired token!'], 400);
        }
        // Update Password
        $admin = User::where('email', $request->email)->first();
        $admin->password = Hash::make($request->password);
        $admin->save();

        // Delete Reset Token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        return response()->json(['success' => 'Password reset successfully!']);
    }
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required',
        ]);
        try {
            $remeber = $request->remember == "on" ? true : false;
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials, $remeber)) {
                if (Auth::user()->role_id == config('constants.ADMIN') || Auth::user()->role_id == config('constants.SUPERADMIN')) {
                    $request->session()->regenerate();

                    $redirectTo = session()->get('url.intended', '/admin/dashboard');
                    return response()->json([
                        'success' => 'Login successful!',
                        'redirect_to' => $redirectTo,
                    ], 200);
                    // return redirect()->intended('/admin/dashboard');
                    // dd(intended('/admin/dashboard'));
                    // return response()->json(['success' => 'Login successful!'], 200);
                }
                Auth::logout();
            }
            return response()->json(['errors' => 'The provided credentials do not match our records.'], 401);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
        // dd($request->all());
        // return back()->withErrors([
        //     'email' => 'The provided credentials do not match our records.',
        // ])->withInput();
    }
    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
