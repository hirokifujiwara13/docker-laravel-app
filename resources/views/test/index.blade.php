@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Queue & Scheduler Testing</h1>
        <p class="mt-2 text-gray-600">Test Laravel's queue system and task scheduler functionality.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <!-- Queue Testing -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                <h2 class="text-xl font-semibold text-gray-800">🔄 Queue System Test</h2>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('test.queue') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Test Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email', 'test@example.com') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Test Message</label>
                        <textarea id="message" name="message" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('message', 'This is a test email from the Laravel Queue system!') }}</textarea>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700">
                        🚀 Queue Test Email
                    </button>
                </form>

                <div class="mt-4 p-3 bg-gray-50 rounded-md">
                    <p class="text-sm text-gray-600">
                        <strong>Note:</strong> Since mail driver is set to 'log', emails will be logged instead of sent.
                        Check the logs to see queue processing.
                    </p>
                </div>
            </div>
        </div>

        <!-- Scheduler Testing -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                <h2 class="text-xl font-semibold text-gray-800">⏰ Scheduler Test</h2>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('test.scheduler') }}">
                    @csrf
                    <p class="text-gray-600 mb-4">
                        Test the Laravel scheduler by manually running the test command.
                    </p>
                    
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700">
                        ⚡ Run Scheduler Test
                    </button>
                </form>

                <div class="mt-4 p-3 bg-gray-50 rounded-md">
                    <p class="text-sm text-gray-600">
                        <strong>In production:</strong> Add this to your crontab:<br>
                        <code class="bg-white px-2 py-1 rounded text-xs">* * * * * cd /var/www && php artisan schedule:run >> /dev/null 2>&1</code>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Queue Status -->
    <div class="mt-6 bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="px-6 py-4 bg-purple-50 border-b border-purple-200">
            <h2 class="text-xl font-semibold text-gray-800">📊 System Status</h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-4">
                <div id="queue-status" class="p-4 bg-gray-50 rounded-md">
                    <h3 class="font-semibold text-gray-700">Queue Status</h3>
                    <p class="text-sm text-gray-600">Loading...</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-md">
                    <h3 class="font-semibold text-gray-700">Worker Command</h3>
                    <p class="text-sm text-gray-600">
                        To process queued jobs, run:<br>
                        <code class="bg-white px-2 py-1 rounded text-xs">php artisan queue:work</code>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Display -->
    <div class="mt-6 bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="px-6 py-4 bg-orange-50 border-b border-orange-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">📝 Recent Logs</h2>
            <button onclick="refreshLogs()" class="px-3 py-1 bg-orange-600 text-white text-sm rounded hover:bg-orange-700">
                🔄 Refresh
            </button>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Scheduler Logs</h3>
                    <div id="scheduler-logs" class="bg-gray-100 p-3 rounded-md h-40 overflow-y-auto">
                        <p class="text-sm text-gray-500">No logs yet. Run scheduler test to see logs.</p>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Queue Logs</h3>
                    <div id="queue-logs" class="bg-gray-100 p-3 rounded-md h-40 overflow-y-auto">
                        <p class="text-sm text-gray-500">No logs yet. Queue an email to see logs.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshLogs() {
    // Fetch queue status
    fetch('{{ route("test.queue-status") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('queue-status').innerHTML = `
                <h3 class="font-semibold text-gray-700">Queue Status</h3>
                <p class="text-sm text-gray-600">${data.status}</p>
                <p class="text-xs text-gray-500">Pending: ${data.pending_jobs} | Failed: ${data.failed_jobs}</p>
                <p class="text-xs text-gray-400">Updated: ${data.timestamp}</p>
            `;
        });

    // Fetch logs
    fetch('{{ route("test.logs") }}')
        .then(response => response.json())
        .then(data => {
            // Update scheduler logs
            const schedulerLogs = document.getElementById('scheduler-logs');
            if (data.scheduler_logs.length > 0) {
                schedulerLogs.innerHTML = data.scheduler_logs.map(log => 
                    `<div class="text-xs mb-1 font-mono">${log}</div>`
                ).join('');
            }

            // Update queue logs
            const queueLogs = document.getElementById('queue-logs');
            if (data.queue_logs.length > 0) {
                queueLogs.innerHTML = data.queue_logs.map(log => 
                    `<div class="text-xs mb-1 font-mono">${log}</div>`
                ).join('');
            }
        });
}

// Auto-refresh every 5 seconds
setInterval(refreshLogs, 5000);

// Initial load
refreshLogs();
</script>
@endsection