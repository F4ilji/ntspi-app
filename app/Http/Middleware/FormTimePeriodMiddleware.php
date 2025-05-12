<?php

namespace App\Http\Middleware;

use App\Containers\Widget\Models\CustomForm;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FormTimePeriodMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $form = CustomForm::select('settings')->find($request->route('id'));

        if (!$form) {
            abort(Response::HTTP_NOT_FOUND, 'Form not found');
        }


        if (!isset($form->settings['period'])) {
            return $next($request);
        }

        $period = $form->settings['period'];

        try {
            $start_time = Carbon::parse($period['start_time']);
            $end_time = Carbon::parse($period['end_time']);
        } catch (\Exception $e) {
            abort(Response::HTTP_BAD_REQUEST, 'Invalid time format');
        }

        $now = Carbon::now();

        if ($now >= $start_time && $now <= $end_time) {
            return $next($request);
        }

        abort(Response::HTTP_FORBIDDEN, 'Form is not available at this time');
    }
}
