<?php

namespace Theofanisv\FilamentOpeningHours\Infolists\Components;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Spatie\OpeningHours\OpeningHours;
use Theofanisv\FilamentOpeningHours\Concerns\FormatsOpeningHours;

class OpeningHoursEntry
{
    use FormatsOpeningHours;

    /**
     * Create a human-readable opening hours display field/section.
     *
     * This generates a comprehensive display section that shows:
     * - Weekly schedule in a readable format
     * - Current open/closed status with next opening/closing time
     * - Special date exceptions
     * - Configuration options (like overflow)
     * - Dynamic icon color based on current status
     *
     * The display automatically handles the spatie/opening-hours data format
     * and provides real-time status information.
     */
    public static function make(string $name): Section
    {
        return Section::make(__('filament-opening-hours::opening-hours.opening_hours'))
            ->schema([
                Group::make([
                    TextEntry::make($name)
                        ->label(__('filament-opening-hours::opening-hours.weekly_schedule'))
                        ->getStateUsing(function ($record) use ($name) {
                            $data = data_get($record, $name, []);

                            return static::formatWeeklySchedule($data);
                        })
                        ->columnSpanFull(),

                    TextEntry::make("{$name}_status")
                        ->label(__('filament-opening-hours::opening-hours.current_status'))
                        ->getStateUsing(function ($record) use ($name) {
                            try {
                                $data = data_get($record, $name, []);

                                if (empty($data)) {
                                    return __('filament-opening-hours::opening-hours.no_hours_defined');
                                }

                                $openingHours = OpeningHours::create($data);

                                return static::formatCurrentStatus($openingHours);
                            } catch (\Exception $e) {
                                return __('filament-opening-hours::opening-hours.invalid_schedule').' '.$e->getMessage();
                            }
                        })
                        ->columnSpanFull(),
                ])->columns(1),

                Group::make([
                    TextEntry::make("{$name}_exceptions")
                        ->label(__('filament-opening-hours::opening-hours.exceptions'))
                        ->getStateUsing(function ($record) use ($name) {
                            $data = data_get($record, $name, []);
                            $exceptions = data_get($data, 'exceptions', []);

                            return static::formatExceptions($exceptions);
                        })
                        ->visible(fn ($record) => ! empty(data_get($record, "{$name}.exceptions", []))),

                    TextEntry::make("{$name}_options")
                        ->label(__('filament-opening-hours::opening-hours.options'))
                        ->getStateUsing(function ($record) use ($name) {
                            $data = data_get($record, $name, []);
                            $options = [];

                            if (data_get($data, 'overflow', false)) {
                                $options[] = __('filament-opening-hours::opening-hours.overflow_enabled');
                            }

                            return empty($options)
                                ? __('filament-opening-hours::opening-hours.no_exceptions')
                                : implode(', ', $options);
                        })
                        ->visible(fn ($record) => (bool) data_get($record, "{$name}.overflow", false)),
                ])->columns(1),
            ])
            ->icon(config('filament-opening-hours.icon', 'heroicon-o-clock'))
            ->iconColor(function ($record) use ($name) {
                try {
                    $data = data_get($record, $name, []);

                    if (empty($data)) {
                        return 'gray';
                    }

                    $openingHours = OpeningHours::create($data);

                    return $openingHours->isOpenAt(now()) ? 'success' : 'danger';
                } catch (\Exception $e) {
                    return 'warning';
                }
            })
            ->collapsible()
            ->columns(1);
    }
}
