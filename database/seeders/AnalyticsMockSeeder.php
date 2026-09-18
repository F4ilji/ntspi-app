<?php

namespace Database\Seeders;

use App\Containers\Analytics\Models\AnalyticsHit;
use App\Containers\Analytics\Models\AnalyticsSession;
use App\Containers\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnalyticsMockSeeder extends Seeder
{
    private const URLS = [
        '/', '/news', '/faculties', '/faculties/1', '/faculties/2', '/faculties/3',
        '/departments', '/departments/1', '/departments/2', '/departments/3',
        '/schedules', '/schedules/1', '/schedules/2', '/events', '/events/1',
        '/additional-educations', '/additional-educations/1',
        '/page/abiturientam', '/page/studentam', '/page/nauchnaya-rabota',
        '/page/ob-institute', '/page/struktura', '/page/obrazovanie',
        '/news/1', '/news/2', '/news/3', '/news/4', '/news/5',
        '/news/6', '/news/7', '/news/8', '/news/9', '/news/10',
        '/departments/1/workers', '/departments/2/workers', '/departments/3/workers',
        '/faculties/1/workers', '/faculties/2/workers',
    ];

    private const BROWSERS = ['Chrome', 'Chrome', 'Chrome', 'YandexBrowser', 'Firefox', 'Safari', 'Edge'];
    private const OSSES = ['Windows', 'Windows', 'Windows', 'macOS', 'Linux', 'Android', 'iOS'];
    private const DEVICES = ['desktop', 'desktop', 'desktop', 'desktop', 'mobile', 'mobile', 'tablet'];
    private const COUNTRIES = ['RU', 'RU', 'RU', 'RU', 'BY', 'KZ', 'UZ'];
    private const CITIES = [
        'Moscow', 'Saint Petersburg', 'Novosibirsk', 'Yekaterinburg', 'Kazan',
        'Nizhny Tagil', 'Chelyabinsk', 'Minsk', 'Almaty', 'Tashkent',
    ];
    private const SOURCES = [
        null, null, null, null, null,
        'https://www.google.com/search?q=ntspi',
        'https://yandex.ru/search/?text=ntspi',
        'https://vk.com/ntspi_official',
        'https://t.me/ntspi_news',
        'https://dzen.ru/a/ntspi',
    ];

    public function run(): void
    {
        $this->command?->info('Creating analytics mock data (30 days)...');

        for ($day = 0; $day < 30; $day++) {
            $date = now()->subDays($day);
            $sessionsCount = rand(8, 40);

            for ($s = 0; $s < $sessionsCount; $s++) {
                $visitorId = Str::uuid();
                $hitsCount = rand(1, 12);
                $browser = self::BROWSERS[array_rand(self::BROWSERS)];
                $os = self::OSSES[array_rand(self::OSSES)];
                $device = self::DEVICES[array_rand(self::DEVICES)];

                $startedAt = $date->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59));
                $lastActivity = $startedAt->copy()->addSeconds(rand(10, 1800));

                $session = AnalyticsSession::create([
                    'visitor_id' => $visitorId,
                    'user_id' => rand(1, 10) > 8 ? User::inRandomOrder()->value('id') : null,
                    'entry_page' => self::URLS[array_rand(self::URLS)],
                    'ip' => long2ip(rand(0, 0xFFFFFF00)),
                    'country' => self::COUNTRIES[array_rand(self::COUNTRIES)],
                    'city' => self::CITIES[array_rand(self::CITIES)],
                    'utm_source' => rand(1, 10) > 7 ? ['google', 'vk', 'telegram', 'dzen'][array_rand(['google', 'vk', 'telegram', 'dzen'])] : null,
                    'utm_medium' => null,
                    'utm_campaign' => null,
                    'browser' => $browser,
                    'os' => $os,
                    'device_type' => $device,
                    'started_at' => $startedAt,
                    'last_activity_at' => $lastActivity,
                ]);

                for ($h = 0; $h < $hitsCount; $h++) {
                    $hitTime = $startedAt->copy()->addSeconds(rand(0, max(1, $lastActivity->timestamp - $startedAt->timestamp)));

                    AnalyticsHit::create([
                        'session_id' => $session->id,
                        'user_id' => $session->user_id,
                        'url' => self::URLS[array_rand(self::URLS)],
                        'referrer' => self::SOURCES[array_rand(self::SOURCES)],
                        'title' => null,
                        'created_at' => $hitTime,
                    ]);
                }
            }
        }

        $this->command?->info('Analytics mock: ' . AnalyticsSession::count() . ' sessions, ' . AnalyticsHit::count() . ' hits');
    }
}
