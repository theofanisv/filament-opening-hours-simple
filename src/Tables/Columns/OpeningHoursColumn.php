<?php

namespace Theofanisv\FilamentOpeningHours\Tables\Columns;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Spatie\OpeningHours\OpeningHours;
use Theofanisv\FilamentOpeningHours\Concerns\FormatsOpeningHours;

class OpeningHoursColumn extends TextColumn
{
    use FormatsOpeningHours;

    protected string $mode = 'compact';

    protected bool $displayStatus = false;

    protected bool $showTooltip = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(function ($state, Model $record) {
            if (empty($state)) {
                return __('filament-opening-hours::opening-hours.not_set');
            }

            return match ($this->mode) {
                'status' => $this->formatStatus($state),
                'today' => static::getTodayHours($state),
                'summary' => static::getCompactSummary($state),
                default => $this->formatCompact($state),
            };
        });

        $this->badge(fn ($state) => $this->displayStatus && ! empty($state));

        $this->color(function ($state, Model $record) {
            if (empty($state)) {
                return 'gray';
            }

            try {
                $openingHours = OpeningHours::create($state);

                return $openingHours->isOpenAt(now()) ? 'success' : 'danger';
            } catch (\Exception $e) {
                return 'warning';
            }
        });

        $this->tooltip(function ($state, Model $record) {
            if (! $this->showTooltip || empty($state)) {
                return null;
            }

            try {
                return strip_tags((string) static::formatWeeklySchedule($state));
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    /**
     * Set the display mode for the column.
     *
     * Available modes:
     * - compact: Show a brief summary (default)
     * - status: Show current open/closed status
     * - today: Show only today's hours
     * - summary: Show days per week summary
     */
    public function mode(string $mode): static
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Show the current status badge.
     */
    public function showStatus(bool $show = true): static
    {
        $this->displayStatus = $show;

        return $this;
    }

    /**
     * Show full schedule in tooltip on hover.
     */
    public function tooltip(bool $show = true): static
    {
        $this->showTooltip = $show;

        return $this;
    }

    /**
     * Format opening hours in compact mode.
     */
    protected function formatCompact(array $data): string
    {
        // Find the most common range across weekdays
        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $ranges = [];

        foreach ($weekdays as $day) {
            $hours = data_get($data, $day, []);
            if (! empty($hours)) {
                $key = is_array($hours) ? implode(',', $hours) : $hours;
                $ranges[$key] = ($ranges[$key] ?? 0) + 1;
            }
        }

        if (empty($ranges)) {
            return static::getCompactSummary($data);
        }

        // Get most common range
        arsort($ranges);
        $commonRange = array_key_first($ranges);
        $count = $ranges[$commonRange];

        if ($count >= 3) {
            return __('filament-opening-hours::opening-hours.monday').'-'.__('filament-opening-hours::opening-hours.friday').": {$commonRange}";
        }

        return static::getCompactSummary($data);
    }

    /**
     * Format the current status.
     */
    protected function formatStatus(array $data): string
    {
        try {
            $openingHours = OpeningHours::create($data);

            if ($openingHours->isOpenAt(now())) {
                return __('filament-opening-hours::opening-hours.open');
            } else {
                return __('filament-opening-hours::opening-hours.closed');
            }
        } catch (\Exception $e) {
            return __('filament-opening-hours::opening-hours.invalid_schedule');
        }
    }
}
