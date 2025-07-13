<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterLog;

class NewsletterDashboardController extends Controller
{
    public function dashboard()
    {
        $sent = NewsletterLog::where('status', 'sent')->count();
        $failed = NewsletterLog::where('status', 'failed')->count();
        $pending = NewsletterLog::where('status', 'pending')->count();

        return view('admin.newsletter_dashboard', compact('sent', 'failed', 'pending'));
    }
}
