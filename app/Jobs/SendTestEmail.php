<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTestEmail implements ShouldQueue
{
    use Queueable;

    public $email;
    public $message;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $message = 'Test email from Laravel Queue')
    {
        $this->email = $email;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        
        // Since we're using log driver for mail, this will be logged instead of sent
        try {
            Mail::raw($this->message, function ($mail) {
                $mail->to($this->email)
                     ->subject('Test Email from Laravel Queue System')
                     ->from('test@laravel-blog.com', 'Laravel Blog');
            });
            
            // Log successful queue processing
            Log::info("Queued email job processed successfully", [
                'email' => $this->email,
                'message' => $this->message,
                'processed_at' => $timestamp
            ]);
            
            // Also log to our custom file
            $logFile = storage_path('logs/queue-test.log');
            file_put_contents($logFile, "[{$timestamp}] Email queued for: {$this->email}\n", FILE_APPEND | LOCK_EX);
            
        } catch (\Exception $e) {
            Log::error('Failed to process email queue job', [
                'email' => $this->email,
                'error' => $e->getMessage(),
                'processed_at' => $timestamp
            ]);
            
            throw $e;
        }
    }
}
