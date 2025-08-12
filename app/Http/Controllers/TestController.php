<?php

namespace App\Http\Controllers;

use App\Jobs\SendTestEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function index()
    {
        return view('test.index');
    }

    public function testQueue(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string'
        ]);

        $email = $request->input('email');
        $message = $request->input('message', 'This is a test email from the Laravel Queue system!');

        // Dispatch the job to the queue
        SendTestEmail::dispatch($email, $message);

        Log::info("Test email job dispatched", [
            'email' => $email,
            'dispatched_at' => now()->format('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Email has been queued successfully! Check the queue logs to verify processing.');
    }

    public function testScheduler()
    {
        // Run the scheduler command manually
        Artisan::call('test:scheduler');
        $output = Artisan::output();

        return redirect()->back()->with('success', 'Scheduler test executed! Output: ' . $output);
    }

    public function showLogs()
    {
        $schedulerLogs = [];
        $queueLogs = [];

        // Read scheduler logs
        $schedulerLogPath = storage_path('logs/scheduler-test.log');
        if (file_exists($schedulerLogPath)) {
            $schedulerLogs = array_reverse(array_filter(explode("\n", file_get_contents($schedulerLogPath))));
        }

        // Read queue logs
        $queueLogPath = storage_path('logs/queue-test.log');
        if (file_exists($queueLogPath)) {
            $queueLogs = array_reverse(array_filter(explode("\n", file_get_contents($queueLogPath))));
        }

        return response()->json([
            'scheduler_logs' => array_slice($schedulerLogs, 0, 10), // Last 10 entries
            'queue_logs' => array_slice($queueLogs, 0, 10), // Last 10 entries
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }

    public function queueStatus()
    {
        // Get queue status from database
        $pendingJobs = \DB::table('jobs')->count();
        $failedJobs = \DB::table('failed_jobs')->count();

        return response()->json([
            'pending_jobs' => $pendingJobs,
            'failed_jobs' => $failedJobs,
            'status' => $pendingJobs > 0 ? 'Jobs in queue' : 'Queue is empty',
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }
}