@php
    $currentValue = $getState();
    $maxValue = $getMaxValue();
    $status = $getProgressStatus();
    $percentage = $getProgressPercentage();
    $label = $getProgressLabel();
    $color = $getProgressColor();

    if (is_array($color)) {
        $color = $color[0] ?? 'gray';
    }

    // Convert rgb() to rgba() with 15% opacity for light background
    $lightBackgroundColor = str_replace('rgb(', 'rgba(', rtrim($color, ')') . ', 0.15)');
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-ta-text block w-full px-3'])
    }}
>
    <div @class(['flex flex-col gap-1.5 w-full'])>
        <div
            @class(['relative w-full rounded-full overflow-hidden'])
            style="height: 0.625rem; background-color: {{ $lightBackgroundColor }};"
            role="progressbar"
            aria-valuenow="{{ $currentValue }}"
            aria-valuemin="0"
            aria-valuemax="{{ $maxValue ?? 100 }}"
            aria-label="{{ $label }}"
        >
            <div
                @class(['h-full rounded-full transition-all duration-300 ease-in-out'])
                style="width: {{ $percentage }}%; background-color: {{ $color }};"
            ></div>
        </div>
        <span @class(['text-xs text-gray-500 dark:text-gray-400'])>
            {{ $label }}
        </span>
    </div>
</div>
