<?php

use Tapp\FilamentProgressBarColumn\Tables\Columns\ProgressBarColumn;
use Tapp\FilamentProgressBarColumn\Tests\Models\TestModel;

beforeEach(function () {
    TestModel::create([
        'name' => 'Product A',
        'stock' => 50,
        'quantity' => 25,
        'max_quantity' => 100,
    ]);

    TestModel::create([
        'name' => 'Product B',
        'stock' => 5,
        'quantity' => 0,
        'max_quantity' => 200,
    ]);

    TestModel::create([
        'name' => 'Product C',
        'stock' => 0,
        'quantity' => 150,
        'max_quantity' => 150,
    ]);
});

it('can create a progress bar column', function () {
    $column = ProgressBarColumn::make('progress')
        ->maxValue(100);

    expect($column)->toBeInstanceOf(ProgressBarColumn::class);
});

it('can set max value', function () {
    $column = ProgressBarColumn::make('progress')
        ->maxValue(100);

    expect($column->getMaxValue())->toBe(100);
});

it('can set max value with closure', function () {
    $model = TestModel::first();

    $column = ProgressBarColumn::make('quantity')
        ->maxValue(fn ($record) => $record->max_quantity);

    $column->record($model);

    expect($column->getMaxValue())->toBe(100);
});

it('can set low threshold', function () {
    $column = ProgressBarColumn::make('progress')
        ->lowThreshold(10);

    expect($column->getLowThreshold())->toBe(10);
});

it('can set custom colors', function () {
    $column = ProgressBarColumn::make('progress')
        ->dangerColor('#ff0000')
        ->warningColor('#ff9900')
        ->successColor('#00ff00');

    // Colors are converted to RGB format
    expect($column->getDangerColor())->toBe('rgb(255, 0, 0)')
        ->and($column->getWarningColor())->toBe('rgb(255, 153, 0)')
        ->and($column->getSuccessColor())->toBe('rgb(0, 255, 0)');
});

it('has default colors', function () {
    $column = ProgressBarColumn::make('progress');

    expect($column->getDangerColor())->toBe('rgb(244, 63, 94)')
        ->and($column->getWarningColor())->toBe('rgb(251, 146, 60)')
        ->and($column->getSuccessColor())->toBe('rgb(34, 197, 94)');
});

it('can set custom labels with strings', function () {
    $column = ProgressBarColumn::make('progress')
        ->dangerLabel('Critical')
        ->warningLabel('Low')
        ->successLabel('Good');

    expect($column->getDangerLabel(0))->toBe('Critical')
        ->and($column->getWarningLabel(5))->toBe('Low')
        ->and($column->getSuccessLabel(50))->toBe('Good');
});

it('can set custom labels with closures', function () {
    $column = ProgressBarColumn::make('stock')
        ->dangerLabel(fn ($state) => "Out: {$state}")
        ->warningLabel(fn ($state) => "Low: {$state}")
        ->successLabel(fn ($state) => "Stock: {$state}");

    expect($column->getDangerLabel(0))->toBe('Out: 0')
        ->and($column->getWarningLabel(5))->toBe('Low: 5')
        ->and($column->getSuccessLabel(50))->toBe('Stock: 50');
});

it('calculates percentage correctly', function () {
    $model = TestModel::where('name', 'Product A')->first();

    $column = ProgressBarColumn::make('stock')
        ->maxValue(100);

    $column->record($model);

    // 50/100 = 50%
    expect($model->stock)->toBe(50);
});

it('handles danger state correctly', function () {
    $model = TestModel::where('stock', 0)->first();

    $column = ProgressBarColumn::make('stock')
        ->maxValue(100)
        ->lowThreshold(10);

    $column->record($model);

    // Stock is 0, should be in danger state
    expect($model->stock)->toBe(0)
        ->and($column->getLowThreshold())->toBe(10);
});

it('handles warning state correctly', function () {
    $model = TestModel::where('stock', 5)->first();

    $column = ProgressBarColumn::make('stock')
        ->maxValue(100)
        ->lowThreshold(10);

    $column->record($model);

    // Stock is 5, which is less than threshold (10), should be in warning state
    expect($model->stock)->toBe(5)
        ->and($model->stock)->toBeLessThan($column->getLowThreshold());
});

it('handles success state correctly', function () {
    $model = TestModel::where('stock', 50)->first();

    $column = ProgressBarColumn::make('stock')
        ->maxValue(100)
        ->lowThreshold(10);

    $column->record($model);

    // Stock is 50, which is greater than threshold (10), should be in success state
    expect($model->stock)->toBe(50)
        ->and($model->stock)->toBeGreaterThan($column->getLowThreshold());
});

it('can use closures for dynamic max values', function () {
    $models = TestModel::all();

    $column = ProgressBarColumn::make('quantity')
        ->maxValue(fn ($record) => $record->max_quantity);

    foreach ($models as $model) {
        $column->record($model);
        expect($column->getMaxValue())->toBe($model->max_quantity);
    }
});

it('can use closures for dynamic thresholds', function () {
    $model = TestModel::first();

    $column = ProgressBarColumn::make('quantity')
        ->maxValue(fn ($record) => $record->max_quantity)
        ->lowThreshold(fn ($record) => (int) ($record->max_quantity * 0.2));

    $column->record($model);

    // 20% of 100 = 20
    expect($column->getLowThreshold())->toBe(20);
});
