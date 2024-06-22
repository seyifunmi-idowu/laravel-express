<?php

namespace App\Services;

// use Illuminate\Support\Facades\Mail;
use SendGrid\Mail\Mail as SendGridMail;
use SendGrid\Mail\From;
use SendGrid\Mail\To;
use SendGrid\Mail\Subject;
use SendGrid\Mail\Content;

use SendGrid\Mail\Cc;
use SendGrid\Mail\Bcc;
use SendGrid\Mail\Mail;
use SendGrid\Mail\Personalization;
use SendGrid\Mail\Header;
use SendGrid\Mail\CustomArg;
use SendGrid\Mail\SendAt;
use SendGrid\Mail\Attachment;
use SendGrid\Mail\Asm;
use SendGrid\Mail\MailSettings;
use SendGrid\Mail\BccSettings;
use SendGrid\Mail\SandBoxMode;
use SendGrid\Mail\BypassListManagement;
use SendGrid\Mail\Footer;
use SendGrid\Mail\SpamCheck;
use SendGrid\Mail\TrackingSettings;
use SendGrid\Mail\ClickTracking;
use SendGrid\Mail\OpenTracking;
use SendGrid\Mail\SubscriptionTracking;
use SendGrid\Mail\Ganalytics;
use SendGrid\Mail\ReplyTo;

use SendGrid;
use Exception;
use Log;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Collection;
use GuzzleHttp\Client;

class NotificationService
{
    public function addUserOneSignal(User $user, string $oneSignalId, array $attributes = []): UserNotification
    {
        // Check if a user notification already exists with the same one_signal_id and user
        $existingNotification = UserNotification::where('one_signal_id', $oneSignalId)
                                                ->where('status', 'ACTIVE')
                                                ->where('user_id', $user->id)
                                                ->first();
        
        if ($existingNotification) {
            return $existingNotification;
        }

        // Deactivate all existing notifications with the same one_signal_id
        $userNotifications = $this->getOneSignal($oneSignalId);
        $actualUserNotification = null;

        foreach ($userNotifications as $notification) {
            if ($notification->user_id == $user->id) {
                $notification->status = 'ACTIVE';
                $actualUserNotification = $notification;
            } else {
                $notification->status = 'INACTIVE';
            }
            $notification->save();
        }

        if ($actualUserNotification) {
            return $actualUserNotification;
        } else {
            return UserNotification::create(array_merge([
                'user_id' => $user->id,
                'one_signal_id' => $oneSignalId,
                'status' => 'ACTIVE',
                'notification_type' => 'PUSH'
            ], $attributes));
        }
    }

    public function getOneSignal(string $oneSignalId): Collection
    {
        return UserNotification::where('one_signal_id', $oneSignalId)->get();
    }

    public function getUserOneSignal(User $user): Collection
    {
        return UserNotification::where('status', 'ACTIVE')
                                ->where('user_id', $user->id)
                                ->get();
    }

    public static function sendSmsMessage(User $user, string $message): array
    {
        $termiiApiKey = config('constants.TERMII_API_KEY');
        $termiiSmsFrom = config('constants.TERMII_SMS_FROM');
        $client = new Client();
        $response = $client->post('https://api.ng.termii.com/api/sms/send', [
            'headers' => [
                'Authorization' => 'Bearer ' . $termiiApiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'to' => [$user->phone_number],
                'sms' => $message,
                'api_key' => $termiiApiKey,
                'channel' => 'dnd',
                'from' => $termiiSmsFrom,
                'type' => 'plain',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public static function sendEmailMessage(array $recipientEmails, $subject, $context, $template=null)
    {
        $senderEmail = config('constants.SENDER_EMAIL');
        $senderName = config('constants.SENDER_NAME');
        $apiKey = config('constants.SENDGRID_API_KEY');
        $env = config('constants.ENV', 'production');

        $htmlContent = view($template, $context)->render();

        $mail = new Mail();

        $personalization0 = new Personalization();
        foreach ($recipientEmails as $recipientEmail) {
            $personalization0->addTo(new To($recipientEmail));
            $mail->addPersonalization($personalization0);
        }
        $subject = $env === 'production' ? $subject : "{$subject} - " . strtoupper($env);
        $template = $template ?: 'emails.general';

        try {
            $mail->setFrom(new From($senderEmail, $senderName));
                        
            $mail->setSubject(new Subject($subject));
            
            $mail->addContent(new Content("text/html", $htmlContent));
            
            $request_body = $mail;
            $sg = new \SendGrid($apiKey);
            $response = $sg->client->mail()->send()->post($request_body);
        
        } catch (Exception $e) {
            Log::error('Cannot send email', ['error' => $e->getMessage()]);
            return false;
        }
    }
}



class EmailManager
{
    protected $senderEmail;
    protected $senderName;
    protected $subject;
    protected $context;
    protected $template;
    protected $apiKey;
    protected $env;

    public function __construct($subject, $context, $template = null)
    {
        $this->senderEmail = env('SENDGRID_SENDER_EMAIL');
        $this->senderName = env('SENDGRID_SENDER_NAME');
        $this->apiKey = env('SENDGRID_API_KEY');
        $this->env = env('APP_ENV', 'local');
        $this->subject = $this->env === 'production' ? $subject : "{$subject} - " . strtoupper($this->env);
        $this->context = $context;
        $this->template = $template ?: 'emails.general'; // default template
    }

}