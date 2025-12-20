<?php

return [
    'opening_hours' => 'Opening Hours',
    'weekly_schedule' => 'Weekly Schedule',
    'current_status' => 'Current Status',
    'exceptions' => 'Exceptions',
    'options' => 'Options',

    // Days of the week
    'monday' => 'Monday',
    'tuesday' => 'Tuesday',
    'wednesday' => 'Wednesday',
    'thursday' => 'Thursday',
    'friday' => 'Friday',
    'saturday' => 'Saturday',
    'sunday' => 'Sunday',

    // Status
    'open' => 'Open',
    'closed' => 'Closed',
    'closes_at' => 'Closes at :time',
    'opens' => 'Opens :when',
    'unknown' => 'Unknown',

    // Messages
    'no_hours_defined' => 'No opening hours defined',
    'no_exceptions' => 'No exceptions defined',
    'invalid_schedule' => 'Invalid schedule data',
    'not_set' => 'Not set',
    'always_closed' => 'Always closed',
    'open_daily' => 'Open daily',
    'days_per_week' => ':count days/week',
    'closed_today' => 'Closed today',
    'today' => 'Today',

    // Form labels and helpers
    'weekly_hours' => 'Weekly Hours',
    'special_dates' => 'Special Dates & Exceptions',
    'define_hours' => 'Define opening hours for each day of the week. Leave empty for closed days.',
    'time_range_format' => 'Enter time ranges separated by commas. Format: HH:MM-HH:MM',
    'time_range_placeholder' => '09:00-17:00,19:00-22:00',
    'exceptions_helper' => 'Define specific dates with different hours or closures. Format: {"2024-12-25": [], "2024-12-31": ["18:00-22:00"]}',
    'overflow_label' => 'Allow overflow (hours crossing midnight)',
    'overflow_enabled' => 'Overflow enabled (crosses midnight)',

    // Validation messages
    'invalid_time_format' => 'Invalid time range format: :range. Use HH:MM-HH:MM format.',
    'invalid_time_logic' => 'Invalid time range logic: :range. Start time should be before end time (unless overflow is enabled).',
    'overlapping_ranges' => 'Time ranges cannot overlap. Please adjust the times.',
    'invalid_exceptions_format' => 'Invalid exceptions format. Use date keys (YYYY-MM-DD or MM-DD) with arrays of time ranges.',
];
