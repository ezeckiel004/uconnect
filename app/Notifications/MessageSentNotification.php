<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageSentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Message $message)
    {
        $this->onQueue('emails');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $sender = $this->message->sender;
        
        return (new MailMessage)
            ->subject("Nouveau message de {$sender->name}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("{$sender->name} a vous envoyé un message:")
            ->line("\"" . substr($this->message->body, 0, 150) . (strlen($this->message->body) > 150 ? '...' : '') . "\"")
            ->action('Lire le message', url('/messages/' . $this->message->conversation_id))
            ->line('Merci d\'utiliser U-Connect!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'body' => substr($this->message->body, 0, 150),
        ];
    }
}
