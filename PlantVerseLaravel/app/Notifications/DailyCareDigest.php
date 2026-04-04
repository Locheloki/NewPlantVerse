<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * DailyCareDigest Notification
 * 
 * Sends users a daily email reminder about their plants that need care today or are overdue.
 * Includes plant names and care task types to help users prioritize their plant care.
 */
class DailyCareDigest extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Number of plants needing care
     */
    protected int $plantCount;

    /**
     * Array of plant care summaries for the email
     * Each entry: ['plant_name' => 'Rose', 'tasks' => ['Water', 'Fertilize']]
     */
    protected array $plantCares;

    /**
     * Create a new notification instance.
     *
     * @param int $plantCount Number of plants needing care
     * @param array $plantCares Array of plant care details
     */
    public function __construct(int $plantCount, array $plantCares)
    {
        $this->plantCount = $plantCount;
        $this->plantCares = $plantCares;
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
        $message = (new MailMessage)
            ->subject('PlantVerse: Daily Care Reminder 🌱')
            ->greeting("Hello {$notifiable->name}!")
            ->line('You have **' . $this->plantCount . ' plant(s)** that need care today or are overdue.')
            ->line(' ');

        // Add each plant's care tasks to the email
        foreach ($this->plantCares as $care) {
            $tasks = implode(', ', $care['tasks']);
            $message->line("🌿 **{$care['plant_name']}** needs: {$tasks}");
        }

        $message
            ->line(' ')
            ->action('View My Plants', route('plants.index'))
            ->line('Thank you for caring for your plants in PlantVerse!')
            ->salutation('Happy Planting,')
            ->salutation('PlantVerse Team 🌍');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'plant_count' => $this->plantCount,
            'plant_cares' => $this->plantCares,
        ];
    }
}
