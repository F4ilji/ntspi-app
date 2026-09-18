<?php

namespace App\Containers\Analytics\UI\WEB\Controllers;

use App\Containers\Analytics\Models\AnalyticsDailyStat;
use App\Containers\Analytics\Models\AnalyticsHit;
use App\Containers\Analytics\Models\AnalyticsSession;
use App\Containers\Analytics\Tasks\GetAnalyticsOverviewTask;
use App\Containers\Analytics\Tasks\GetDailyStatsTask;
use App\Containers\Analytics\Tasks\GetDevicesTask;
use App\Containers\Analytics\Tasks\GetNavigationSectionsTask;
use App\Containers\Analytics\Tasks\GetReferrersTask;
use App\Containers\Analytics\Tasks\GetSectionDetailTask;
use App\Containers\Analytics\Tasks\GetTopPagesTask;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly GetAnalyticsOverviewTask $overviewTask,
        private readonly GetTopPagesTask $topPagesTask,
        private readonly GetDailyStatsTask $dailyStatsTask,
        private readonly GetReferrersTask $referrersTask,
        private readonly GetDevicesTask $devicesTask,
        private readonly GetNavigationSectionsTask $navigationSectionsTask,
        private readonly GetSectionDetailTask $sectionDetailTask,
    ) {}

    public function __invoke(Request $request): Response
    {
        $days = (int) $request->query('days', 30);

        return inertia()->render('Dashboard/Analytics', [
            'overview' => $this->overviewTask->run(days: $days),
            'topPages' => $this->topPagesTask->run(days: $days),
            'dailyStats' => $this->dailyStatsTask->run(days: $days),
            'referrers' => $this->referrersTask->run(days: $days),
            'devices' => $this->devicesTask->run(days: $days),
            'navigation' => $this->navigationSectionsTask->run(days: $days),
            'period' => $days,
        ]);
    }

    public function section(Request $request): Response
    {
        $prefix = $request->query('prefix', '/');
        $days = (int) $request->query('days', 30);

        $section = $this->sectionDetailTask->run(prefix: $prefix, days: $days);

        return inertia()->render('Dashboard/AnalyticsSectionDetail', [
            'section' => $section,
            'period' => $days,
        ]);
    }

    public function clear(): RedirectResponse
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        AnalyticsHit::truncate();
        AnalyticsSession::truncate();
        AnalyticsDailyStat::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return redirect()->route('dashboard.analytics.index')
            ->with('success', 'Данные аналитики очищены');
    }
}
