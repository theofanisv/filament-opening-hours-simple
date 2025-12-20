<?php

namespace Theofanisv\FilamentOpeningHours\Concerns;

use Illuminate\Support\HtmlString;
use Spatie\OpeningHours\OpeningHours;

trait FormatsOpeningHours
{
    /**
     * Format the weekly schedule for display.
     */
    protected static function formatWeeklySchedule(array $data): HtmlString|string
    {
        if (empty($data)) {
            return __('filament-opening-hours::opening-hours.no_hours_defined');
        }

        $days = [
            'monday' => __('filament-opening-hours::opening-hours.monday'),
            'tuesday' => __('filament-opening-hours::opening-hours.tuesday'),
            'wednesday' => __('filament-opening-hours::opening-hours.wednesday'),
            'thursday' => __('filament-opening-hours::opening-hours.thursday'),
            'friday' => __('filament-opening-hours::opening-hours.friday'),
            'saturday' => __('filament-opening-hours::opening-hours.saturday'),
            'sunday' => __('filament-opening-hours::opening-hours.sunday'),
        ];

        $schedule = collect($days)->map(function ($dayName, $dayKey) use ($data) {
            $hours = data_get($data, $dayKey, []);

            if (empty($hours)) {
                return "<span class='text-gray-500'>{$dayName}: ".__('filament-opening-hours::opening-hours.closed').'</span>';
            }

            $hoursText = is_array($hours) ? implode(', ', $hours) : $hours;

            return "<strong>{$dayName}:</strong> {$hoursText}";
        })->implode('<br>');

        return new HtmlString($schedule);
    }

    /**
     * Format the current status (Open/Closed) with next change time.
     */
    protected static function formatCurrentStatus(OpeningHours $openingHours): HtmlString|string
    {
        try {
            $now = now();

            if ($openingHours->isOpenAt($now)) {
                $nextClose = $openingHours->nextClose($now);
                $closesAt = $nextClose ? $nextClose->format('H:i') : __('filament-opening-hours::opening-hours.unknown');

                return new HtmlString(
                    "<span class='text-green-600 font-semibold'>".__('filament-opening-hours::opening-hours.open').'</span>'.
                    "<br><span class='text-sm text-gray-500'>".__('filament-opening-hours::opening-hours.closes_at', ['time' => $closesAt]).'</span>'
                );
            } else {
                $nextOpen = $openingHours->nextOpen($now);

                if ($nextOpen) {
                    $opensAt = $nextOpen->format('l H:i');

                    return new HtmlString(
                        "<span class='text-red-600 font-semibold'>".__('filament-opening-hours::opening-hours.closed').'</span>'.
                        "<br><span class='text-sm text-gray-500'>".__('filament-opening-hours::opening-hours.opens', ['when' => $opensAt]).'</span>'
                    );
                } else {
                    return new HtmlString("<span class='text-red-600 font-semibold'>".__('filament-opening-hours::opening-hours.closed').'</span>');
                }
            }
        } catch (\Exception $e) {
            return __('filament-opening-hours::opening-hours.invalid_schedule').' '.$e->getMessage();
        }
    }

    /**
     * Format exceptions for display.
     */
    protected static function formatExceptions(array $exceptions): HtmlString|string
    {
        if (empty($exceptions)) {
            return __('filament-opening-hours::opening-hours.no_exceptions');
        }

        $exceptionsList = collect($exceptions)->map(function ($hours, $date) {
            if (empty($hours)) {
                return "<strong>{$date}:</strong> ".__('filament-opening-hours::opening-hours.closed');
            }

            $hoursText = is_array($hours) ? implode(', ', $hours) : $hours;

            return "<strong>{$date}:</strong> {$hoursText}";
        })->implode('<br>');

        return new HtmlString($exceptionsList);
    }

    /**
     * Format a single time range.
     */
    protected static function formatTimeRange(string $range): string
    {
        // Already in HH:MM-HH:MM format, just return it
        return $range;
    }

    /**
     * Get compact summary of opening hours for table display.
     */
    protected static function getCompactSummary(array $data): string
    {
        if (empty($data)) {
            return __('filament-opening-hours::opening-hours.not_set');
        }

        $daysOpen = collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])
            ->filter(fn ($day) => ! empty(data_get($data, $day, [])))
            ->count();

        if ($daysOpen === 0) {
            return __('filament-opening-hours::opening-hours.always_closed');
        }

        if ($daysOpen === 7) {
            return __('filament-opening-hours::opening-hours.open_daily');
        }

        return __('filament-opening-hours::opening-hours.days_per_week', ['count' => $daysOpen]);
    }

    /**
     * Get today's hours for display.
     */
    protected static function getTodayHours(array $data): string
    {
        $today = strtolower(now()->format('l')); // monday, tuesday, etc.
        $hours = data_get($data, $today, []);

        if (empty($hours)) {
            return __('filament-opening-hours::opening-hours.closed_today');
        }

        $hoursText = is_array($hours) ? implode(', ', $hours) : $hours;

        return __('filament-opening-hours::opening-hours.today').': '.$hoursText;
    }
}
