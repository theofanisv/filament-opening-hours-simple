<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Time Format
    |--------------------------------------------------------------------------
    |
    | The default time format used for display. Uses PHP date() format.
    |
    */
    'time_format' => 'H:i',

    /*
    |--------------------------------------------------------------------------
    | Allow Overflow by Default
    |--------------------------------------------------------------------------
    |
    | Whether to allow midnight-crossing time ranges by default.
    |
    */
    'allow_overflow' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Collapsed State
    |--------------------------------------------------------------------------
    |
    | Whether the opening hours section should be collapsed by default in forms.
    |
    */
    'collapsed' => true,

    /*
    |--------------------------------------------------------------------------
    | Icon
    |--------------------------------------------------------------------------
    |
    | The default icon for opening hours sections.
    | Uses Heroicon name format (e.g., 'heroicon-o-clock').
    |
    */
    'icon' => 'heroicon-o-clock',

    /*
    |--------------------------------------------------------------------------
    | Table Column Display Mode
    |--------------------------------------------------------------------------
    |
    | Default display mode for table columns.
    | Options: compact, status, today, summary
    |
    */
    'table_column_mode' => 'compact',
];
