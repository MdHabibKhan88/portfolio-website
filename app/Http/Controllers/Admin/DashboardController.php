<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Volunteer;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            ['key' => 'a_total_projects', 'count' => Project::count(), 'icon' => 'fa-diagram-project'],
            ['key' => 'a_total_skills', 'count' => Skill::count(), 'icon' => 'fa-code'],
            ['key' => 'a_total_certificates', 'count' => Certificate::count(), 'icon' => 'fa-certificate'],
            ['key' => 'a_total_volunteers', 'count' => Volunteer::count(), 'icon' => 'fa-hand-holding-heart'],
            ['key' => 'a_total_messages', 'count' => ContactMessage::count(), 'icon' => 'fa-envelope'],
        ];

        // Messages per month for the last 6 months (grouped in PHP so it works on any database).
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'));
        $counts = ContactMessage::where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->pluck('created_at')->groupBy(fn ($d) => $d->format('Y-m'))->map->count();
        $chart = [
            'labels' => $months->values()->all(),
            'data' => $months->map(fn ($m) => $counts[$m] ?? 0)->values()->all(),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'chart' => $chart,
            'activities' => Activity::latest()->latest('id')->take(10)->get(),
            'unread' => ContactMessage::where('is_read', false)->count(),
        ]);
    }
}
