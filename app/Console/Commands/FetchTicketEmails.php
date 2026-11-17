<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facedes\Client;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Models\Notification;

class FetchTicketEmails extends Command
{
    protected $signature = 'ticket:fetch-emails';
    protected $description = 'Fetch unread email replies and insert into ticket_replies';

        public function handle()
        {
            $client = Client::account('default');
            $client->connect();

            $folder = $client->getFolder('INBOX'); 
            $messages = $folder->messages()->unseen()->get();

            foreach ($messages as $message) {
                $subject = (string) $message->getSubject();
                $fromEmail = $message->getFrom()[0]->mail ?? null;
                $fromName = $message->getFrom()[0]->personal ?? $fromEmail;

                if (preg_match('/\[(?:#TC-|TK-)([A-Za-z0-9-]+)\]/', $subject, $matches)) {
                    $ticketId = $matches[1];

                    $ticket = Ticket::where('ticket_id', $ticketId)
                        ->orWhere('ticket_id', '#TC-' . $ticketId)
                        ->first();

                    if ($ticket && !in_array($ticket->status, ['Close', 'Closed'])) {
                        $rawBody = $message->getTextBody() ?: strip_tags($message->getHTMLBody());
                        $cleanBody = $this->extractNewReplyText($rawBody);

                        if (!empty(trim($cleanBody))) {
                            $senderUser = User::where('email', $fromEmail)->first();
                            $senderRole = $senderUser ? $senderUser->role : 'Guest';
                            $senderName = $senderUser ? ($senderUser->full_name ?? $senderUser->name) : $fromName;

                            TicketReply::create([
                                'ticket_id' => $ticket->ticket_id,
                                'user_id' => $senderUser->user_id ?? null,
                                'sender_name' => $senderName,
                                'sender_role' => in_array($senderRole, ['Admin', 'Super Admin']) ? $senderRole : 'Staff',
                                'message' => nl2br(e(trim($cleanBody))),
                            ]);

                            $this->triggerNotification($ticket, $senderUser, $senderName, $cleanBody);
                        }
                    }
                }

                $message->setFlag('Seen');
            }

            $this->info('Email replies processed successfully.');
        }

        private function extractNewReplyText($text)
        {
            $lines = explode("\n", str_replace("\r\n", "\n", $text));
            $cleanLines = [];

            foreach ($lines as $line) {
                $trimmed = trim($line);

                if (
                    preg_match('/^On\s.+wrote:$/i', $trimmed) ||
                    str_starts_with($trimmed, '>') ||
                    str_starts_with($trimmed, 'From:') ||
                    str_starts_with($trimmed, '-----Original Message-----')
                ) {
                    break;
                }
                $cleanLines[] = $line;
            }

            return implode("\n", $cleanLines);
        }

        private function triggerNotification($ticket, $senderUser, $senderName, $cleanBody)
        {
            $displayNo = (str_starts_with($ticket->ticket_id, '#') ? '' : '#TC-') . $ticket->ticekt_id;
            $isFromAdmin = $senderUser && in_array($senderUser->role, ['Admin', 'Super Admin']);

            if ($isFromAdmin) {
                if ($ticket->user_id) {
                    Notification::create([
                        'user_id'               => $ticket->user_id,
                        'notification_type'     => 'admin_reply',
                        'notification_title'    => 'New Reply on' . $displayNo,
                        'notification_message'  => $senderName . ' replied via email: ' . \Illuminate\Support\Str::limit(trim($cleanBody), 60),
                        'notification_link'     => route('messages.index', ['ticket_id' => $ticket->ticket_id]),
                    ]);
                }
            } else {
                if (!empty($ticket->pic)) {
                    $picUser = User::where('user_id', $ticket->pic)->first();
                    if ($picUser) {
                        $link = ($picUser->role === 'Super Admin')
                            ? route('superadmin.messages.index', ['ticket_id' => $ticket->ticket_id])
                            : route('admin.messages.show', $ticket->ticket_id);

                        Notification::create([
                            'user_id'              => $picUser->user_id,
                            'notification_type'    => 'new_reply',
                            'notification_title'   => 'New Email Reply on ' . $displayNo,
                            'notification_message' => $senderName . ' replied: ' . \Illuminate\Support\Str::limit(trim($cleanBody), 60),
                            'notification_link'    => $link,
                        ]);
                    }
                }
            }
        }
}