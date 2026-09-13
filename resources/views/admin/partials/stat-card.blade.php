{{-- Compact outline stat card. Neutral by design (no accent color) so a row
     of these never turns into a rainbow of KPI colors — color is reserved
     for status-pill.blade.php, which carries real meaning. --}}
<div class="bg-white border border-[#ebebee] rounded-xl px-4 py-3 flex items-center gap-3 min-w-[180px]">
  <div class="w-9 h-9 rounded-lg border border-[#ebebee] flex items-center justify-center text-[#09090b] shrink-0">
    <i class="fas {{ $icon ?? 'fa-chart-bar' }} text-sm"></i>
  </div>
  <div class="min-w-0">
    <div class="text-lg font-bold text-[#09090b] leading-tight">{{ $value }}</div>
    <div class="text-xs text-gray-500 truncate">{{ $label }}</div>
  </div>
</div>
