<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OutlookMailService;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;

class SyncOutlookTickets extends Command
{
    protected $signature = 'tickets:sync-outlook';

    protected $description = 'Pull new emails from Outlook and import them into the ticketing system';

    public function handle(OutlookMailService $outlook)
    {
        $emails = $outlook->getUnreadEmails();

        if (empty($emails)) {
            $this->info('No new emails.');
            return;
        }

        foreach ($emails as $email) {
            $graphMessageId     = $email['id'];
            $senderEmail        = $email['from']['emailAddres']['address'];
            $senderName         = $email['from']['emailAddress']['name'] ?? $senderEmail;
            $subject            = $email['subject'] ?? '(No Title)';
            $bodyContent        = trim(strip_tags($email['body']['content']));

            $existingUser       = User::where('email', $senderEmail)->first();
            $fallbackUserId     = $existingUser ? $existingUser->id : 1;

            preg_match('/\[Ticket #(\d+)\]/', $subject, $matches);
            $ticket = !empty($matches) ? Ticket::where('ticket_id', $matches[1])->first() : null;

            if (!$ticket) {
                Ticket::create([
                    'user_name'     => $senderName,
                    'user_email'    => $senderEmail,
                    'title'         => $subject,
                    'description'   => $bodyContent,
                    'type'          => 'Incident',
                    'category'      => 'Email',
                    'date'          => now()->toDateString(),
                    'status'        => 'New',
                    'priority'      => 'Medium',
                    'user_id'       => $fallbackUserId,
                ]);

            } else {

                TicketReply::create([
                    'ticket_id'     => $ticket->ticket_id,
                    'user_id'       => $existingUser?->id,
                    'sender_name'   => $senderName,
                    'sender_role'   => 'User',
                    'message'       => $bodyContent,
                ]);

                if ($ticket->status === 'Close') {
                    $ticket->update(['status' => 'Processing']);
                }
            }

            $outlook->markAsRead($graphMessageId);
        }

        $this->info('Finished checking and synchronizing Outlook emails.');
    }
}