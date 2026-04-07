<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Schedules\CreateScheduleAction;
use App\Containers\Dashboard\Actions\Schedules\UpdateScheduleAction;
use App\Containers\Dashboard\Actions\Schedules\DeleteScheduleAction;
use App\Containers\Dashboard\Actions\Schedules\ListSchedulesAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreScheduleRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateScheduleRequest;
use App\Containers\Schedule\Models\Schedule;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ScheduleController extends Controller
{
    public function __construct(
        private readonly ListSchedulesAction $listSchedulesAction,
        private readonly CreateScheduleAction $createScheduleAction,
        private readonly UpdateScheduleAction $updateScheduleAction,
        private readonly DeleteScheduleAction $deleteScheduleAction,
    ) {}

    /**
     * Показывает список расписаний
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'educational_group_id', 'education_form_id']);

        $data = $this->listSchedulesAction->run($filters);

        return Inertia::render('Dashboard/Schedules/Index', $data);
    }

    /**
     * Показывает форму создания расписания
     */
    public function create(): \Inertia\Response
    {
        $data = $this->listSchedulesAction->run([]);

        return Inertia::render('Dashboard/Schedules/Create', [
            'educationalGroups' => $data['educationalGroups'],
            'educationForms' => $data['educationForms'],
        ]);
    }

    /**
     * Создает новое расписание
     */
    public function store(StoreScheduleRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            // Обработка файла
            if (!empty($validated['file'][0]['path'])) {
                $file = $validated['file'][0]['path'];
                $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . Carbon::now()->timestamp) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('schedules', $filename, 'public');

                $validated['file'] = [
                    [
                        'title' => $validated['file'][0]['title'],
                        'path' => $path,
                    ],
                ];
            }

            $this->createScheduleAction->run($validated);

            return redirect()->route('dashboard.schedules.index')
                ->with('success', 'Расписание успешно создано!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании расписания: ' . $e->getMessage());
        }
    }

    /**
     * Показывает форму редактирования расписания
     */
    public function edit(Schedule $schedule): \Inertia\Response
    {
        $schedule->load(['educationalGroup.faculty']);

        $data = $this->listSchedulesAction->run([]);

        return Inertia::render('Dashboard/Schedules/Edit', [
            'schedule' => $schedule,
            'educationalGroups' => $data['educationalGroups'],
            'educationForms' => $data['educationForms'],
        ]);
    }

    /**
     * Обновляет существующее расписание
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        try {
            $validated = $request->validated();

            // Обработка нового файла если загружен
            if (!empty($validated['file'][0]['path'])) {
                $file = $validated['file'][0]['path'];
                $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . Carbon::now()->timestamp) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('schedules', $filename, 'public');

                $validated['file'] = [
                    [
                        'title' => $validated['file'][0]['title'],
                        'path' => $path,
                    ],
                ];
            } else {
                // Оставляем старый файл
                unset($validated['file']);
            }

            $this->updateScheduleAction->run($schedule, $validated);

            return redirect()->route('dashboard.schedules.index')
                ->with('success', 'Расписание успешно обновлено!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении расписания: ' . $e->getMessage());
        }
    }

    /**
     * Удаляет расписание
     */
    public function destroy(Schedule $schedule): RedirectResponse
    {
        try {
            $this->deleteScheduleAction->run($schedule);

            return redirect()->route('dashboard.schedules.index')
                ->with('success', 'Расписание успешно удалено!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении расписания: ' . $e->getMessage());
        }
    }
}
