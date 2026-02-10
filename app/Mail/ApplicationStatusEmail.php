<?php

namespace App\Mail;

use App\Models\SchoolApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusEmail extends Mailable
{
    use Queueable, SerializesModels;

    public SchoolApplication $application;
    public string $parentName;

    public function __construct(SchoolApplication $application, string $parentName)
    {
        $this->application = $application;
        $this->parentName = $parentName;
    }

    public function build()
    {
        $status = ucfirst((string) $this->application->status);
        $subject = 'Application ' . $status . ' - ' . (string) $this->application->title;

        return $this
            ->subject($subject)
            ->view('email.applicationStatusEmail')
            ->from(get_settings('smtp_user'), get_settings('system_title'));
    }
}

