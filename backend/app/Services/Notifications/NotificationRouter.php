<?php

namespace App\Services\Notifications;

use App\Models\Customer;
use App\Models\Subscription;
use App\Services\Notifications\Sms\SmsDriver;
use App\Services\Notifications\Whatsapp\WhatsappDriver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationRouter
{
    public function __construct(
        private readonly SmsDriver $sms,
        private readonly WhatsappDriver $wa,
    ) {}

    public function sendEmailIfAllowed(Subscription $sub, Customer $customer, string $subject, string $content): array
    {
        if (!$sub->email_consent) {
            return ['status' => 'skipped', 'reason' => 'email_consent=false'];
        }
        if (!$customer->email) {
            return ['status' => 'skipped', 'reason' => 'missing_email'];
        }

        // Starter: use log mailer, later switch to SMTP
        Mail::raw($content, function ($m) use ($customer, $subject) {
            $m->to($customer->email)->subject($subject);
        });

        return ['status' => 'sent'];
    }

    public function sendSmsIfAllowed(Subscription $sub, Customer $customer, string $content): array
    {
        if (!$sub->sms_consent) {
            return ['status' => 'skipped', 'reason' => 'sms_consent=false'];
        }
        if (!$customer->phone) {
            return ['status' => 'skipped', 'reason' => 'missing_phone'];
        }

        $this->sms->send($customer->phone, $content);
        return ['status' => 'sent'];
    }

    public function sendWhatsappIfAllowed(Subscription $sub, Customer $customer, string $content): array
    {
        if (!$sub->whatsapp_consent) {
            return ['status' => 'skipped', 'reason' => 'whatsapp_consent=false'];
        }
        if (!$customer->phone) {
            return ['status' => 'skipped', 'reason' => 'missing_phone'];
        }

        // WABLAS driver itself will skip invalid/landline; we record it here too
        try {
            $this->wa->send($customer->phone, $content);
        } catch (\Throwable $e) {
            Log::error('[NOTIF] whatsapp failed', ['error' => $e->getMessage()]);
            return ['status' => 'failed', 'reason' => $e->getMessage()];
        }

        return ['status' => 'sent_or_skipped_by_driver'];
    }
}