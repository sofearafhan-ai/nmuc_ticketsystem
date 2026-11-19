<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class OutlookMailService
{
    protected string $tenantId;
    protected string $clientId;
    protected string $clientSecret;
    protected string $supportEmail;

    public function __construct()
    {
        $this->tenantId     = config('services.msgraph.tenant_id');
        $this->clientId     = config('services.msgraph.client_id');
        $this->clientSecret = config('services.msgraph.client_secret');
        $this->supportEmail = config('services.msgraph.support_email');
    }

    public function getAccessToken(): string
    {
        return Cache::remember('msgraph_access_token', 3000, function () {
            $response = Http::asForm()->post("https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token", [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope'         => 'https://graph.microsoft.com/.default',
                'grant_type'    => 'client_credentials',
            ]);

            return $response->json('access_token');
        });
    }

    public function sendEmail(string $toEmail, string $subject, string $bodyText): bool
    {
        $token = $this->getAccessToken();

        if (!$token) {
            dd('FAILED TO OBTAIN TOKEN: Check the Client ID, Client Secret, or Tenant ID in .env');
        }

        $response = Http::withToken($token)
            ->post("https://graph.microsoft.com/v1.0/users/{$this->supportEmail}/sendMail", [
                'message' => [
                    'subject' => $subject,
                    'body' => [
                        'contentType' => 'Text',
                        'content'     => $bodyText,
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $toEmail,
                            ],
                        ],
                    ],
                ],
            ]);

        if (!$response->successful()) {
            dd([
                'status' => $response->status(),
                'error'  => $response->json(),
            ]);
        }

        return true;
    }

    public function getUnreadEmails(): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->get("https://graph.microsoft.com/v1.0/users/{$this->supportEmail}/mailFolders/inbox/messages", [
                '$filter' => 'isRead eq false',
                '$select' => 'id,conversationId,subject,from,body,receivedDateTime',
                '$top'    => 20,
            ]);

        return $response->json('value') ?? [];
    }

    public function markAsRead(string $graphMessageId): void
    {
        $token = $this->getAccessToken();

        Http::withToken($token)
            ->patch("https://graph.microsoft.com/v1.0/users/{$this->supportEmail}/messages/{$graphMessageId}", [
                'isRead' => true,
            ]);
    }

    public function replyToEmail(string $graphMessageId, string $replyText): bool
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post("https://graph.microsoft.com/v1.0/users/{$this->supportEmail}/messages/{$graphMessageId}/reply", [
                'comment' => $replyText,
            ]);

        return $response->successful();
    }
}