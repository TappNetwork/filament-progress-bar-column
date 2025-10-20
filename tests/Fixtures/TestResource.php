<?php

namespace Tapp\FilamentProgressBarColumn\Tests\Fixtures;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Tapp\FilamentProgressBarColumn\Tables\Columns\ProgressBarColumn;
use Tapp\FilamentProgressBarColumn\Tests\Models\TestModel;

class TestResource extends Resource
{
    protected static ?string $model = TestModel::class;

    public static function table(Table $table): Table
    {
            return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name'),
                ProgressBarColumn::make('stock')
                    ->maxValue(100)
                    ->lowThreshold(10),
                ProgressBarColumn::make('quantity')
                    ->maxValue(fn ($record) => $record->max_quantity ?? 100)
                    ->lowThreshold(15)
                    ->dangerColor('#ff0000')
                    ->warningColor('#ff9900')
                    ->successColor('#00ff00')
                    ->dangerLabel('Out of stock')
                    ->warningLabel(fn ($state) => "{$state} low")
                    ->successLabel(fn ($state) => "{$state} available"),
            ]);
    }
}

