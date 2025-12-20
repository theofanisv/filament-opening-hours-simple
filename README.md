# Filament Opening Hours

[![Latest Version on Packagist](https://img.shields.io/packagist/v/theofanisv/filament-opening-hours.svg?style=flat-square)](https://packagist.org/packages/theofanisv/filament-opening-hours)
[![Total Downloads](https://img.shields.io/packagist/dt/theofanisv/filament-opening-hours.svg?style=flat-square)](https://packagist.org/packages/theofanisv/filament-opening-hours)

A comprehensive Filament v4 package providing UI components for [spatie/opening-hours](https://github.com/spatie/opening-hours).

## Features

- 📝 **Form Input Component** - Intuitive day-by-day editing with real-time validation
- 👁️ **Infolist Entry Component** - Beautiful display with current open/closed status
- 📊 **Table Column Component** - Compact display with multiple modes
- ⏰ **Multiple Time Ranges** - Support for multiple opening periods per day
- 📅 **Exceptions** - Override hours for specific dates (holidays, special events)
- 🌙 **Overflow Support** - Handle businesses open past midnight
- ✅ **Real-time Validation** - Instant feedback on time format and overlapping ranges
- 🌍 **Translation Ready** - Full localization support

## Installation

You can install the package via composer:

```bash
composer require theofanisv/filament-opening-hours
```

## Quick Start

### Form Component

```php
use Theofanisv\FilamentOpeningHours\Forms\Components\OpeningHoursInput;

OpeningHoursInput::make('opening_hours')
    ->label('Business Hours')
    ->required();
```

### Infolist Component

```php
use Theofanisv\FilamentOpeningHours\Infolists\Components\OpeningHoursEntry;

OpeningHoursEntry::make('opening_hours')
    ->label('Operating Hours');
```

### Table Column

```php
use Theofanisv\FilamentOpeningHours\Tables\Columns\OpeningHoursColumn;

OpeningHoursColumn::make('opening_hours')
    ->mode('status')
    ->showStatus()
    ->tooltip();
```

## Documentation

Full documentation will be available soon.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
