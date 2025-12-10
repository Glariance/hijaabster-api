<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\SmtpSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.pages.settings.index');
    }

    public function profilePage()
    {
        return view('admin.pages.settings.profile-page');
    }
    public function profilePost(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'file' => 'nullable|mimes:jpeg,jpg,png,gif|max:2048',
        ]);
        try {
            $user = User::find(Auth::id());
            $user->name = $request->name;
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $imageName = time() . '.' . $image->extension();
                $image->move(public_path('uploads/admin/profile'), $imageName);
                $user->image = "/uploads/admin/profile/" . $imageName;
            }
            $user->save();
            return response()->json(['success' => 'Profile updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }
    public function changePasswordPage()
    {
        return view('admin.pages.settings.change-password-page');
    }
    public function changepasswordPost(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed',
        ]);
        try {
            $user = User::find(Auth::id());
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json(['success' => 'Password changed successfully'], 200);
            } else {
                return response()->json(['errors' => 'Current password is incorrect'], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }
    public function smtpPage()
    {
        $smtp = SmtpSetting::find(1);
        return view('admin.pages.settings.smtp-page', compact('smtp'));
    }
    public function smtpPost(Request $request)
    {
        $request->validate([
            'mail_driver' => 'required',
            'mail_host' => 'required',
            'mail_port' => 'required',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'mail_encryption' => 'required',
            'mail_from_address' => 'required',
        ]);
        try {
            SmtpSetting::updateOrCreate(['id' => 1], $request->all());
            $responseMail = $this->sendTestMail(Auth::user()->email, 'SMTP Test Email', 'This is a test email to verify SMTP configuration.');
            Artisan::call('config:clear');
            return response()->json(['success' => 'SMTP settings updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }
    public function generalPage()
    {
        $settings = GeneralSetting::all();
        return view('admin.pages.settings.general-page', compact('settings'));
    }
    public function generalPost(Request $request)
    {
        // Ensure settings is always an array
        $settings = $request->settings ?? [];

        // Update Existing Settings
        foreach ($settings as $key => $data) {
            $setting = GeneralSetting::where('key', $key)->first();

            if ($setting) {
                if ($setting->type === 'file' && $request->hasFile("settings.$key")) {
                    $data = $request->file("settings.$key")->store('settings', 'public');
                }
                $setting->update(['value' => $data]);
            }
        }

        // Handle New Settings
        if ($request->has('new_settings')) {
            foreach ($request->new_settings as $newSetting) {
                GeneralSetting::create([
                    'key' => $newSetting['key'],
                    'type' => $newSetting['type'],
                    'value' => $newSetting['type'] === 'boolean' ? 0 : null, // Default value for new settings
                ]);
            }
        }

        return response()->json(['success' => 'Settings updated successfully'], 200);
    }

    public function generaldelete($id)
    {
        try {
            $setting = GeneralSetting::find($id);
            if (!$setting) {
                return response()->json(['errors' => 'Setting not found'], 404);
            }

            if ($setting->type === 'file' && $setting->value) {
                $filePath = storage_path('app/public/' . $setting->value);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $setting->delete();
            return response()->json(['success' => 'Setting deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }
}
