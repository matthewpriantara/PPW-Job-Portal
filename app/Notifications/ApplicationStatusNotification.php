<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($application, $status)
    {
        $this->application = $application;
        $this->status = $status;
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
        $jobTitle = $this->application->jobVacancy->job->title;
        $applicantName = $this->application->user->name;

        if ($notifiable->id === $this->application->user_id) {
            // Notification to applicant
            return (new MailMessage)
                ->subject('Application Status Update')
                ->line('Your application for the job "' . $jobTitle . '" has been ' . $this->status . '.')
                ->action('View Details', url('/dashboard'))
                ->line('Thank you for applying!');
        } else {
            // Notification to admin
            return (new MailMessage)
                ->subject('Application Status Updated')
                ->line('You have ' . $this->status . ' the application from ' . $applicantName . ' for the job "' . $jobTitle . '".')
                ->action('View Application', url('/applications/' . $this->application->id))
                ->line('Thank you for managing applications!');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $jobTitle = $this->application->jobVacancy->job->title;
        $applicantName = $this->application->user->name;

        if ($notifiable->id === $this->application->user_id) {
            return [
                'message' => 'Your application for the job "' . $jobTitle . '" has been ' . $this->status . '.',
                'status' => $this->status,
                'job_title' => $jobTitle,
                'application_id' => $this->application->id,
            ];
        } else {
            return [
                'message' => 'You have ' . $this->status . ' the application from ' . $applicantName . ' for the job "' . $jobTitle . '".',
                'status' => $this->status,
                'applicant_name' => $applicantName,
                'job_title' => $jobTitle,
                'application_id' => $this->application->id,
            ];
        }
    }
}
