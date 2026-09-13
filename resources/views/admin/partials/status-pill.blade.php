@php
  // Single source of truth for status colors across the whole admin section.
  // Reuse these five meanings everywhere instead of introducing new colors.
  $colorMap = [
    'success' => ['bg-emerald-50', 'border-emerald-200', 'text-emerald-600', 'bg-emerald-500'],
    'danger'  => ['bg-rose-50', 'border-rose-200', 'text-rose-600', 'bg-rose-500'],
    'warning' => ['bg-amber-50', 'border-amber-200', 'text-amber-600', 'bg-amber-500'],
    'info'    => ['bg-blue-50', 'border-blue-200', 'text-blue-600', 'bg-blue-500'],
    'neutral' => ['bg-gray-50', 'border-gray-200', 'text-gray-500', 'bg-gray-400'],
  ];
  [$bg, $border, $text, $dot] = $colorMap[$color ?? 'neutral'];
@endphp
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $bg }} {{ $border }} {{ $text }}">
  <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
  {{ $label }}
</span>
