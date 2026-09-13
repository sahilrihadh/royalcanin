@php
  $palette = ['#3b82f6', '#f1416c', '#f5a524', '#17c964', '#8b5cf6', '#06b6d4', '#eab308', '#ec4899'];
  $bg = $palette[crc32($name ?? '') % count($palette)];
  $initials = collect(explode(' ', trim($name ?? '')))
    ->filter()
    ->map(fn($word) => mb_substr($word, 0, 1))
    ->take(2)
    ->implode('');
  $avatarSize = $size ?? 40;
@endphp
<div class="inline-flex items-center justify-center rounded-full text-white font-semibold text-sm uppercase shrink-0"
     style="background-color: {{ $bg }}; width: {{ $avatarSize }}px; height: {{ $avatarSize }}px;">
  {{ $initials !== '' ? $initials : '?' }}
</div>
