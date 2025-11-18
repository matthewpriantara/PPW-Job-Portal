<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApplicantNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;
    /**
     * Create a new notification instance.
     */
    public function __construct($application)
    {
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Application')
            ->line('New applicant has applied for the job: ' . $this->application->jobVacancy->job->title)
            ->line('Applicant Name: ' . $this->application->user->name)
            ->action('Show Application Detail', url('/applications/' . $this->application->id . '/cv'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'New applicant has applied for the job: ' . $this->application->jobVacancy->job->title,
            'applicant_name' => $this->application->user->name,
            'application_id' => $this->application->id,
            'job_title' => $this->application->jobVacancy->job->title,
        ];
    }
}
