# Filament Progress Column

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tapp/filament-progress-bar-column.svg?style=flat-square)](https://packagist.org/packages/tapp/filament-progress-bar-column)
[![Total Downloads](https://img.shields.io/packagist/dt/tapp/filament-progress-bar-column.svg?style=flat-square)](https://packagist.org/packages/tapp/filament-progress-bar-column)

Visualize progress at a glance with color-coded progress bars for your Filament table. Perfect for inventory tracking, task completion, storage usage, event capacity, budget monitoring, and more with customizable thresholds and automatic status indicators.

![Progress Column Screenshot](https://raw.githubusercontent.com/TappNetwork/filament-progress-bar-column/main/docs/column.png)

## Features

- Visual progress bar representation
- Automatic status detection based on customizable thresholds
- Customizable colors for each status (danger/warning/success)
- Customizable labels for each status
- Works for any use case: inventory, tasks, storage, capacity, budgets, etc.
- Accessible with proper ARIA attributes

## Installation

You can install the package via Composer:

```bash
composer require tapp/filament-progress-bar-column
```

## Add the plugin in your TailwindCSS config file

To enable the TailwindCSS plugin classes, add the plugin to `content` in your `tailwindcss.config.js` file:

```js
export default {
    // ...
    content: [
        // ...
        './vendor/tapp/filament-progress-bar-column/resources/views/**/*.blade.php',
        './vendor/tapp/filament-progress-bar-column/src/**/*.php',
    ],
    // ...
}
```

## Usage

The column is simple by design. Just two required things:

1. The database column name (e.g., `'stock'`, `'quantity'`, `'tasks_completed'`)
2. The `maxValue()` method to calculate percentages

Everything else has sensible defaults but can be customized!

### Basic Usage

Add the `ProgressBarColumn` column to your Filament table:

```php
use Tapp\FilamentProgressBarColumn\Tables\Columns\ProgressBarColumn;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ... other columns
            
            ProgressBarColumn::make('stock')
                ->maxValue(100),
        ]);
}
```

### With Low Threshold

Define when the value should be considered "low" (warning state):

```php
ProgressBarColumn::make('stock')
    ->maxValue(100)
    ->lowThreshold(10),
```

### Custom Colors

Customize the colors for each status:

```php
ProgressBarColumn::make('stock')
    ->maxValue(100)
    ->lowThreshold(10)
    ->dangerColor('rgb(239, 68, 68)')  // Red - when value ≤ 0
    ->warningColor('rgb(245, 158, 11)') // Amber - when value ≤ threshold
    ->successColor('rgb(34, 197, 94)'), // Green - when value > threshold
```

You can use any valid CSS color format (hex, rgb, rgba, etc.):

```php
ProgressBarColumn::make('stock')
    ->maxValue(100)
    ->dangerColor('#ef4444')
    ->warningColor('#f59e0b')
    ->successColor('#22c55e'),
```

### Custom Labels

Customize the labels displayed for each status:

```php
ProgressBarColumn::make('stock')
    ->maxValue(100)
    ->lowThreshold(10)
    ->dangerLabel(fn ($state) => 'Out of stock')
    ->warningLabel(fn ($state) => "{$state} low stock")
    ->successLabel(fn ($state) => "{$state} in stock"),
```

### Dynamic Values

Use closures for dynamic max values and thresholds:

```php
ProgressBarColumn::make('stock')
    ->maxValue(fn ($record) => $record->warehouse_capacity)
    ->lowThreshold(fn ($record) => $record->reorder_point),
```

### Multiple Use Cases

#### Inventory/Stock Tracking
```php
ProgressBarColumn::make('quantity')
    ->label('Stock')
    ->maxValue(100)
    ->lowThreshold(15)
    ->dangerLabel(fn ($state) => 'Out of stock')
    ->warningLabel(fn ($state) => "{$state} low stock")
    ->successLabel(fn ($state) => "{$state} in stock"),
```

#### Task Completion
```php
ProgressBarColumn::make('tasks_completed')
    ->label('Progress')
    ->maxValue(fn ($record) => $record->total_tasks)
    ->lowThreshold(fn ($record) => $record->total_tasks * 0.3)
    ->successLabel(fn ($state, $record) => "{$state}/{$record->total_tasks} tasks"),
```

#### Storage Usage
```php
ProgressBarColumn::make('storage_used_gb')
    ->label('Storage')
    ->maxValue(fn ($record) => $record->storage_quota_gb)
    ->lowThreshold(fn ($record) => $record->storage_quota_gb * 0.8)
    ->successLabel(fn ($state, $record) => "{$state}GB / {$record->storage_quota_gb}GB"),
```

#### Event Capacity
```php
ProgressBarColumn::make('registered_attendees')
    ->label('Capacity')
    ->maxValue(fn ($record) => $record->max_capacity)
    ->lowThreshold(fn ($record) => $record->max_capacity * 0.9)
    ->dangerLabel(fn ($state) => "No registration")
    ->warningLabel(fn ($state) => "{$state} - Almost full!")
    ->successLabel(fn ($state) => "{$state} registered"),
```

### Complete Example

```php
use Tapp\FilamentProgressBarColumn\Tables\Columns\ProgressBarColumn;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('id'),

            TextColumn::make('name'),
            
            ProgressBarColumn::make('stock')
                ->label('Current Stock')
                ->maxValue(fn ($record) => $record->max_capacity)
                ->lowThreshold(20)
                ->dangerColor('#dc2626')
                ->warningColor('#f97316')
                ->successColor('#16a34a')
                ->dangerLabel(fn ($state) => 'Out of stock')
                ->warningLabel(fn ($state) => "{$state} low stock")
                ->successLabel(fn ($state) => "{$state} in stock"),
            
            TextColumn::make('price')
                ->money('usd'),
        ]);
}
```

### Methods

#### `maxValue(int | Closure $value)`
Set the maximum value for the progress bar. This is used to calculate the percentage.

#### `lowThreshold(int | Closure $value)`
Set the threshold below which the status is considered "warning". If not set, only "danger" (≤0) and "success" (>0) states are used.

#### `dangerColor(string | array | Closure $color)`
Set the color for the danger state (when value ≤ 0). Default: `rgb(244, 63, 94)` (pink/red).

#### `warningColor(string | array | Closure $color)`
Set the color for the warning state (when value ≤ threshold). Default: `rgb(251, 146, 60)` (orange).

#### `successColor(string | array | Closure $color)`
Set the color for the success state (when value > threshold). Default: `rgb(34, 197, 94)` (green).

#### `dangerLabel(string | Closure $label)`
Set the label for danger state. The closure receives the current value as `$state`. Default: `fn ($state) => "{$state}"`.

#### `warningLabel(string | Closure $label)`
Set the label for warning state. The closure receives the current value as `$state`. Default: `fn ($state) => "{$state}"`.

#### `successLabel(string | Closure $label)`
Set the label for success state. The closure receives the current value as `$state`. Default: `fn ($state) => "{$state}"`.

### Status Logic

The column automatically determines the status based on the current value:

- **Danger**: Current value ≤ 0
- **Warning**: Current value > 0 AND current value ≤ low threshold (if set)
- **Success**: Current value > low threshold (or > 0 if no threshold is set)

### Progress Bar Calculation

The progress bar width is calculated as:
```
percentage = (currentValue / maxValue) * 100
```

The percentage is clamped between `0` and `100`.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Tapp Network](https://github.com/TappNetwork)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
