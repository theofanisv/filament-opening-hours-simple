<?php

namespace Theofanisv\FilamentOpeningHours\Concerns;

trait ValidatesOpeningHours
{
    /**
     * Validate time range format (HH:MM-HH:MM).
     */
    protected static function validateTimeRange(string $range): bool
    {
        // Match HH:MM-HH:MM format (supports 24:00 for overflow)
        $pattern = '/^([0-1]?[0-9]|2[0-4]):([0-5][0-9])-([0-1]?[0-9]|2[0-4]):([0-5][0-9])$/';

        if (! preg_match($pattern, $range, $matches)) {
            return false;
        }

        $startHour = (int) $matches[1];
        $startMinute = (int) $matches[2];
        $endHour = (int) $matches[3];
        $endMinute = (int) $matches[4];

        // Validate hour ranges (0-24, where 24:00 is allowed for overflow)
        if ($startHour > 24 || $endHour > 24) {
            return false;
        }

        // Validate minute ranges (0-59)
        if ($startMinute > 59 || $endMinute > 59) {
            return false;
        }

        // Special case: 24:00 is only valid as end time and with minutes = 00
        if ($startHour === 24 && $startMinute !== 0) {
            return false;
        }

        if ($endHour === 24 && $endMinute !== 0) {
            return false;
        }

        return true;
    }

    /**
     * Validate time range logic (start before end, unless overflow).
     */
    protected static function validateTimeRangeLogic(string $range): bool
    {
        if (! preg_match('/^(\d{1,2}):(\d{2})-(\d{1,2}):(\d{2})$/', $range, $matches)) {
            return false;
        }

        $startHour = (int) $matches[1];
        $startMinute = (int) $matches[2];
        $endHour = (int) $matches[3];
        $endMinute = (int) $matches[4];

        $startTotalMinutes = $startHour * 60 + $startMinute;
        $endTotalMinutes = $endHour * 60 + $endMinute;

        // For overflow cases (crossing midnight), end time can be less than start time
        // This is valid for ranges like "22:00-02:00"
        if ($endTotalMinutes <= $startTotalMinutes) {
            // This might be overflow - we'll allow it but it should be validated
            // in context with the overflow setting
            return true;
        }

        return $startTotalMinutes < $endTotalMinutes;
    }

    /**
     * Check if time ranges overlap.
     */
    protected static function hasOverlappingRanges(array $timeRanges): bool
    {
        if (count($timeRanges) < 2) {
            return false;
        }

        $parsedRanges = [];
        foreach ($timeRanges as $range) {
            if (! preg_match('/^(\d{1,2}):(\d{2})-(\d{1,2}):(\d{2})$/', $range, $matches)) {
                continue;
            }

            $startHour = (int) $matches[1];
            $startMinute = (int) $matches[2];
            $endHour = (int) $matches[3];
            $endMinute = (int) $matches[4];

            $parsedRanges[] = [
                'start' => $startHour * 60 + $startMinute,
                'end' => $endHour * 60 + $endMinute,
                'original' => $range,
            ];
        }

        // Sort ranges by start time
        usort($parsedRanges, fn ($a, $b) => $a['start'] <=> $b['start']);

        // Check for overlaps
        for ($i = 0; $i < count($parsedRanges) - 1; $i++) {
            $current = $parsedRanges[$i];
            $next = $parsedRanges[$i + 1];

            // Handle overflow cases
            if ($current['end'] <= $current['start']) {
                // Current range crosses midnight
                continue; // Skip overlap check for overflow ranges
            }

            if ($next['end'] <= $next['start']) {
                // Next range crosses midnight
                continue; // Skip overlap check for overflow ranges
            }

            // Normal case: check if current end time overlaps with next start time
            // Use > to catch boundary touching (e.g., 09:00-12:00, 12:00-15:00)
            if ($current['end'] > $next['start']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate exceptions data format.
     */
    protected static function validateExceptions(mixed $value): bool
    {
        if (empty($value)) {
            return true;
        }

        if (! is_array($value)) {
            return false;
        }

        foreach ($value as $date => $hours) {
            // Validate date format
            if (! static::validateDateFormat($date)) {
                return false;
            }

            // Validate hours format
            if (! is_array($hours)) {
                return false;
            }

            foreach ($hours as $range) {
                if (! static::validateTimeRange($range)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Validate date format (YYYY-MM-DD or MM-DD).
     */
    protected static function validateDateFormat(string $date): bool
    {
        // YYYY-MM-DD format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $dateTime = \DateTime::createFromFormat('Y-m-d', $date);

            return $dateTime && $dateTime->format('Y-m-d') === $date;
        }

        // MM-DD format (recurring)
        if (preg_match('/^\d{2}-\d{2}$/', $date)) {
            $dateTime = \DateTime::createFromFormat('m-d', $date);

            return $dateTime && $dateTime->format('m-d') === $date;
        }

        return false;
    }
}
