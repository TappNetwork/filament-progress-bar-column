<?php

namespace Tapp\FilamentProgressBarColumn\Tables\Columns;

use Filament\Tables\Columns\Column;
use Spatie\Color\Hex;

class ProgressBarColumn extends Column
{
    protected string $view = 'filament-progress-bar-column::columns.progress-bar-column';

    protected int | \Closure | null $maxValue = null;

    protected int | \Closure | null $lowThreshold = null;

    protected string | array | \Closure | null $dangerColor = null;

    protected string | array | \Closure | null $warningColor = null;

    protected string | array | \Closure | null $successColor = null;

    protected string | \Closure | null $dangerLabel = null;

    protected string | \Closure | null $warningLabel = null;

    protected string | \Closure | null $successLabel = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Set default colors
        $this->dangerColor = 'rgb(244, 63, 94)'; // red/pink
        $this->warningColor = 'rgb(251, 146, 60)'; // orange
        $this->successColor = 'rgb(34, 197, 94)'; // green

        // Set default labels with descriptive text
        $this->dangerLabel = fn ($state) => $state <= 0 ? 'out of stock' : "{$state}";
        $this->warningLabel = fn ($state) => $state !== null ? "{$state} low stock" : '0 low stock';
        $this->successLabel = fn ($state) => $state !== null ? "{$state} in stock" : '0 in stock';
    }

    public function maxValue(int | \Closure $value): static
    {
        $this->maxValue = $value;

        return $this;
    }

    public function getMaxValue(): ?int
    {
        return $this->evaluate($this->maxValue);
    }

    public function lowThreshold(int | \Closure $value): static
    {
        $this->lowThreshold = $value;

        return $this;
    }

    public function getLowThreshold(): ?int
    {
        return $this->evaluate($this->lowThreshold);
    }

    public function dangerColor(string | array | \Closure $color): static
    {
        $this->dangerColor = $color;

        return $this;
    }

    public function getDangerColor(): string | array | null
    {
        return $this->normalizeColor($this->evaluate($this->dangerColor));
    }

    public function warningColor(string | array | \Closure $color): static
    {
        $this->warningColor = $color;

        return $this;
    }

    public function getWarningColor(): string | array | null
    {
        return $this->normalizeColor($this->evaluate($this->warningColor));
    }

    public function successColor(string | array | \Closure $color): static
    {
        $this->successColor = $color;

        return $this;
    }

    public function getSuccessColor(): string | array | null
    {
        return $this->normalizeColor($this->evaluate($this->successColor));
    }

    /**
     * Normalize color to rgb() format.
     * Accepts hex (#FF0000), rgb (rgb(255, 0, 0)), or array formats.
     */
    protected function normalizeColor(string | array | null $color): string | array | null
    {
        if ($color === null || is_array($color)) {
            return $color;
        }

        // If it's already in rgb() format, return as is
        if (str_starts_with($color, 'rgb')) {
            return $color;
        }

        // If it's a hex color, convert to rgb()
        if (str_starts_with($color, '#')) {
            try {
                $rgb = Hex::fromString($color)->toRgb();
                return sprintf('rgb(%d, %d, %d)', $rgb->red(), $rgb->green(), $rgb->blue());
            } catch (\Exception $e) {
                // If conversion fails, return the original color
                return $color;
            }
        }

        return $color;
    }

    public function dangerLabel(string | \Closure $label): static
    {
        $this->dangerLabel = $label;

        return $this;
    }

    public function getDangerLabel(int | float | null $currentValue): ?string
    {
        return $this->evaluate($this->dangerLabel, [
            'state' => $currentValue,
        ]);
    }

    public function warningLabel(string | \Closure $label): static
    {
        $this->warningLabel = $label;

        return $this;
    }

    public function getWarningLabel(int | float | null $currentValue): ?string
    {
        return $this->evaluate($this->warningLabel, [
            'state' => $currentValue,
        ]);
    }

    public function successLabel(string | \Closure $label): static
    {
        $this->successLabel = $label;

        return $this;
    }

    public function getSuccessLabel(int | float | null $currentValue): ?string
    {
        return $this->evaluate($this->successLabel, [
            'state' => $currentValue,
        ]);
    }

    public function getProgressStatus(): string
    {
        $currentValue = $this->getState();
        $lowThreshold = $this->getLowThreshold();

        if ($currentValue === null || $currentValue <= 0) {
            return 'danger';
        }

        if ($lowThreshold !== null && $currentValue <= $lowThreshold) {
            return 'warning';
        }

        return 'success';
    }

    public function getProgressPercentage(): float
    {
        $currentValue = $this->getState();
        $maxValue = $this->getMaxValue();

        if ($currentValue === null || $maxValue === null || $maxValue <= 0) {
            return 0;
        }

        return min(100, max(0, ($currentValue / $maxValue) * 100));
    }

    public function getProgressLabel(): string
    {
        $currentValue = $this->getState();
        $status = $this->getProgressStatus();

        return match ($status) {
            'danger' => $this->getDangerLabel($currentValue) ?? '0',
            'warning' => $this->getWarningLabel($currentValue) ?? '0',
            'success' => $this->getSuccessLabel($currentValue) ?? '0',
        };
    }

    public function getProgressColor(): string | array
    {
        $status = $this->getProgressStatus();

        return match ($status) {
            'danger' => $this->getDangerColor(),
            'warning' => $this->getWarningColor(),
            'success' => $this->getSuccessColor(),
        };
    }
}

