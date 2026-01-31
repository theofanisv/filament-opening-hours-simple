<?php

namespace Theofanisv\FilamentOpeningHours\Forms\Components;

use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component as LivewireComponent;
use Theofanisv\FilamentOpeningHours\Concerns\ValidatesOpeningHours;

class OpeningHoursInput
{
    use ValidatesOpeningHours;

    /**
     * Create an intuitive opening hours input field/section.
     *
     * This generates a comprehensive form section for editing opening hours data
     * compatible with spatie/opening-hours package. Users can:
     * - Set hours for each day of the week (comma-separated time ranges)
     * - Define exceptions for specific dates
     * - Enable overflow for businesses open past midnight
     *
     * Data format examples:
     * - Single range: "09:00-17:00"
     * - Multiple ranges: "09:00-12:00,14:00-18:00"
     * - Closed: "" (empty string)
     * - Overflow: "22:00-02:00" (with overflow option enabled)
     */
    public static function make(string $name): Section
    {
        return Section::make(__('filament-opening-hours::opening-hours.opening_hours'))
            ->statePath($name)
            ->schema([
                Fieldset::make(__('filament-opening-hours::opening-hours.weekly_hours'))
                    ->schema([
                        static::createDayInput('monday', __('filament-opening-hours::opening-hours.monday')),
                        static::createDayInput('tuesday', __('filament-opening-hours::opening-hours.tuesday')),
                        static::createDayInput('wednesday', __('filament-opening-hours::opening-hours.wednesday')),
                        static::createDayInput('thursday', __('filament-opening-hours::opening-hours.thursday')),
                        static::createDayInput('friday', __('filament-opening-hours::opening-hours.friday')),
                        static::createDayInput('saturday', __('filament-opening-hours::opening-hours.saturday')),
                        static::createDayInput('sunday', __('filament-opening-hours::opening-hours.sunday')),
                    ])
                    ->columns(1),

                Fieldset::make(__('filament-opening-hours::opening-hours.special_dates'))
                    ->schema([
                        KeyValue::make('exceptions')
                            ->label(__('filament-opening-hours::opening-hours.exceptions'))
                            ->helperText(__('filament-opening-hours::opening-hours.exceptions_helper'))
                            ->keyLabel(__('Date (YYYY-MM-DD or MM-DD)'))
                            ->valueLabel(__('Hours (comma-separated ranges)'))
                            ->addActionLabel(__('Add exception'))
                            ->reorderable()
                            ->rules([
                                'nullable',
                                'array',
                                fn () => function (string $attribute, $value, Closure $fail) {
                                    if (! static::validateExceptions($value)) {
                                        $fail(__('filament-opening-hours::opening-hours.invalid_exceptions_format'));
                                    }
                                },
                            ])
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if (! empty($state) && ! static::validateExceptions($state)) {
                                    // Invalid exceptions detected
                                    return;
                                }
                            }),

                        Checkbox::make('overflow')
                            ->label(__('filament-opening-hours::opening-hours.overflow_label')),
                    ])
                    ->columns(1),
            ])
            ->icon(config('filament-opening-hours.icon', 'heroicon-o-clock'))
            ->description(__('filament-opening-hours::opening-hours.define_hours'))
            ->collapsed(config('filament-opening-hours.collapsed', true))
            ->columns(1);
    }

    /**
     * Create a validated day input field for opening hours.
     */
    protected static function createDayInput(string $day, string $label): TextInput
    {
        return TextInput::make($day)
            ->label($label)
            ->placeholder(__('filament-opening-hours::opening-hours.time_range_placeholder'))
            ->helperText(__('filament-opening-hours::opening-hours.time_range_format'))
            ->formatStateUsing(function ($state) {
                return is_array($state) ? implode(',', $state) : $state;
            })
            ->dehydrateStateUsing(fn ($state) => $state ? array_filter(explode(',', str_replace(' ', '', $state))) : [])
            ->rules([
                'nullable',
                'string',
                fn () => function (string $attribute, $value, Closure $fail) {
                    if (empty($value)) {
                        return; // Allow empty values (closed day)
                    }

                    $timeRanges = array_filter(explode(',', str_replace(' ', '', $value)));

                    foreach ($timeRanges as $range) {
                        if (! static::validateTimeRange($range)) {
                            $fail(__('filament-opening-hours::opening-hours.invalid_time_format', ['range' => $range]));

                            return;
                        }

                        if (! static::validateTimeRangeLogic($range)) {
                            $fail(__('filament-opening-hours::opening-hours.invalid_time_logic', ['range' => $range]));

                            return;
                        }
                    }

                    // Check for overlapping ranges
                    if (static::hasOverlappingRanges($timeRanges)) {
                        $fail(__('filament-opening-hours::opening-hours.overlapping_ranges'));
                    }
                },
            ])
            ->live()
            ->afterStateUpdated(function ($state, $set, $get) {
                // Real-time validation feedback
                if (! empty($state)) {
                    $timeRanges = array_filter(explode(',', str_replace(' ', '', $state)));
                    foreach ($timeRanges as $range) {
                        if (! static::validateTimeRange($range)) {
                            // Invalid format detected - field will show error
                            return;
                        }
                    }
                }
            });
    }
}
