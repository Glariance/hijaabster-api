<?php

namespace App\Http\Controllers;

use App\Mail\UniversalMailable;
use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

abstract class Controller
{

    public function smtpSettings()
    {
        $smtp = SmtpSetting::first();
        if ($smtp) {
            // Dynamically update SMTP settings for this request
            Config::set('mail.default', 'smtp'); // Ensure SMTP is used
            Config::set('mail.mailers.smtp', [
                'transport'  => 'smtp',
                'host'       => $smtp->mail_host,
                'port'       => $smtp->mail_port,
                'username'   => $smtp->mail_username,
                'password'   => $smtp->mail_password,
                'encryption' => $smtp->mail_encryption,
            ]);
            Config::set('mail.from.address', $smtp->mail_from_address);
        }
    }

    public function sendTestMail($testEmail, $subject, $emailBody)
    {
        try {
            $this->smtpSettings();
            // Send the test email
            Mail::raw($emailBody, function ($mail) use ($testEmail, $subject) {
                $mail->to($testEmail)
                    ->subject($subject);
            })->queue();

            $this->forSuccessResponse('Test email sent successfully!');
            // return response()->json(['success'=>true,'message' => 'Test email sent successfully!']);
        } catch (\Exception $e) {
            $this->forErrorResponse('Mail sending failed: ' . $e->getMessage(), 500);
            // return response()->json(['success' => false, 'message' => 'Mail sending failed: ' . $e->getMessage()], 500);
        }
    }

    public function sendMailHelper($email, $mailClass, $data)
    {
        $this->smtpSettings();
        // Send the test email
        Mail::to($email)->send(new $mailClass($data));
    }
    public function universalSendMail($email, $subject, $body, $view)
    {
        $this->smtpSettings();
        // Send the test email
        Mail::to($email)->send(new UniversalMailable($subject, $body, $view));
    }

    public function forSuccessResponse($message)
    {
        return response()->json(['success' => true, 'message' => $message], 200);
    }
    public function forErrorResponse($message, $code)
    {
        return response()->json(['success' => false, 'message' => $message], $code);
    }
}
