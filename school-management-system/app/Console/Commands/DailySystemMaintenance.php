<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SystemHealthNotification;

class DailySystemMaintenance extends Command
{
    protected $signature = 'system:maintain 
                            {--dry-run : Simulate tasks without making changes}';

    protected $description = 'Perform daily maintenance: clean sessions, archive logs, generate reports, notify admins';

    public function handle()
    {
        $this->info("Running Daily System Maintenance...");

        $dryRun = $this->option('dry-run');

        $this->task('Deleting expired sessions', function () use ($dryRun) {
            if ($dryRun) return true;
            DB::table('sessions')->where('last_activity', '<', now()->subDays(7)->timestamp)->delete();
            return true;
        });

        $this->task('Archiving old log files', function () use ($dryRun) {
            $logPath = storage_path('logs');
            $archivePath = storage_path('logs/archive');
            if (!File::exists($archivePath)) File::makeDirectory($archivePath, 0755, true);

            $archived = 0;
            foreach (File::files($logPath) as $file) {
                if ($file->getCTime() < now()->subDays(7)->timestamp && $file->getExtension() === 'log') {
                    if (!$dryRun) {
                        File::move($file->getRealPath(), $archivePath . '/' . $file->getFilename());
                    }
                    $archived++;
                }
            }
            $this->info("Archived $archived log files.");
            return true;
        });

        $this->task('Generating monthly sales report', function () use ($dryRun) {
            if ($dryRun) return true;

            $totalSales = DB::table('orders')
                ->whereMonth('created_at', now()->subMonth()->month)
                ->sum(DB::raw('quantity * 100'));

            File::put(
                storage_path('app/monthly_report.txt'),
                "Total Sales Last Month: ₹$totalSales"
            );

            return true;
        });

        $this->task('Sending system health notifications', function () use ($dryRun) {
            if ($dryRun) return true;

            $admins = DB::table('users')->where('type', 'admin')->pluck('email');
            foreach ($admins as $email) {
                Mail::to($email)->send(new SystemHealthNotification());
            }

            return true;
        });

        $this->info('Maintenance tasks completed' . ($dryRun ? ' (Dry Run Mode)' : ''));
    }

    protected function task(string $description, callable $callback)
    {
        $this->line("• $description...");
        try {
            $callback();
            $this->line(" Success");
        } catch (\Throwable $e) {
            $this->line("Failed: {$e->getMessage()}");
        }
    }
}
