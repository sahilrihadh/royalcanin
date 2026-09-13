@php
  $current = $paginator->currentPage();
  $last = $paginator->lastPage();
  $window = 2;
  $start = max(1, $current - $window);
  $end = min($last, $current + $window);

  $pageBtn = 'inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-lg border border-[#ebebee] text-sm text-[#09090b] hover:bg-gray-50';
  $pageBtnActive = 'bg-gray-100 border-gray-300 font-bold text-[#09090b]';
  $pageBtnDisabled = 'text-gray-300 pointer-events-none';
@endphp

<div class="flex flex-wrap items-center justify-between gap-3 p-4">
  <div class="flex items-center gap-2 text-sm text-gray-500">
    Show
    <select class="border border-[#ebebee] rounded-lg text-sm px-2 py-1 text-[#09090b] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" onchange="
      const url = new URL(window.location.href);
      url.searchParams.set('per_page', this.value);
      url.searchParams.delete('page');
      window.location.href = url.toString();
    ">
      @foreach([10, 20, 50] as $option)
        <option value="{{ $option }}" {{ ($perPage ?? 10) == $option ? 'selected' : '' }}>{{ $option }}</option>
      @endforeach
    </select>
    per page
  </div>

  <div class="text-sm text-gray-500">
    {{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }}
  </div>

  <div class="flex items-center gap-1.5">
    <a href="{{ $current > 1 ? $paginator->url($current - 1) : '#' }}" class="{{ $pageBtn }} {{ $current <= 1 ? $pageBtnDisabled : '' }}">
      <i class="fas fa-arrow-left text-xs"></i>
    </a>

    @if($start > 1)
      <a href="{{ $paginator->url(1) }}" class="{{ $pageBtn }}">1</a>
      @if($start > 2)
        <span class="text-gray-300 px-1">&hellip;</span>
      @endif
    @endif

    @for($p = $start; $p <= $end; $p++)
      <a href="{{ $paginator->url($p) }}" class="{{ $pageBtn }} {{ $p == $current ? $pageBtnActive : '' }}">{{ $p }}</a>
    @endfor

    @if($end < $last)
      @if($end < $last - 1)
        <span class="text-gray-300 px-1">&hellip;</span>
      @endif
      <a href="{{ $paginator->url($last) }}" class="{{ $pageBtn }}">{{ $last }}</a>
    @endif

    <a href="{{ $current < $last ? $paginator->url($current + 1) : '#' }}" class="{{ $pageBtn }} {{ $current >= $last ? $pageBtnDisabled : '' }}">
      <i class="fas fa-arrow-right text-xs"></i>
    </a>
  </div>
</div>
