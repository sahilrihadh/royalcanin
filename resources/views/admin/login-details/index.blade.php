@extends('admin.layouts.master')

@section('title', 'Login Details')
@section('page-title', 'User Login Details')

@section('breadcrumb')
<li class="breadcrumb-item active">Login Details</li>
@endsection

@section('content')

<div class="flex items-center justify-end gap-2 mb-4">
  <a href="{{ route('admin.login-details.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
     class="inline-flex items-center gap-2 border border-[#ebebee] text-[#09090b] hover:bg-gray-50 font-semibold text-sm px-4 py-2 rounded-lg">
    <i class="fas fa-file-excel"></i> Export Excel
  </a>
  <a href="{{ route('admin.login-details.attendance') }}"
     class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-4 py-2 rounded-lg">
    <i class="fas fa-user-check"></i> Unique Attendance
  </a>
</div>

<div class="bg-white border border-[#ebebee] rounded-xl overflow-hidden">
  <div class="p-4 border-b border-[#ebebee]">
    <form method="GET" action="{{ route('admin.login-details.index') }}" class="flex flex-wrap items-end gap-3">
      <div>
        <label class="block text-xs font-medium text-[#09090b] mb-1">Start Date</label>
        <input type="date" name="start_date" value="{{ $startDate }}"
               class="border border-[#ebebee] rounded-lg text-sm px-3 py-2 text-[#09090b] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
      </div>
      <div>
        <label class="block text-xs font-medium text-[#09090b] mb-1">End Date</label>
        <input type="date" name="end_date" value="{{ $endDate }}"
               class="border border-[#ebebee] rounded-lg text-sm px-3 py-2 text-[#09090b] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
      </div>
      <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-4 py-2 rounded-lg">
        <i class="fas fa-filter"></i> Filter
      </button>
      <a href="{{ route('admin.login-details.index') }}"
         class="inline-flex items-center gap-2 border border-[#ebebee] text-[#09090b] hover:bg-gray-50 font-semibold text-sm px-4 py-2 rounded-lg">
        <i class="fas fa-rotate-left"></i> Reset
      </a>
    </form>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
      <thead>
        <tr class="bg-gray-50">
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">#</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">User</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Email</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Login Time</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Logout Time</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Duration</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Status</th>
          <th class="px-4 py-3 w-12"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($loginDetails as $index => $detail)
        <tr class="border-b border-[#ebebee] hover:bg-gray-50">
          <td class="px-4 py-3 text-[#09090b]">{{ $loginDetails->firstItem() + $index }}</td>
          <td class="px-4 py-3 text-[#09090b] font-medium">{{ $detail->user->full_name ?? 'Unknown' }}</td>
          <td class="px-4 py-3 text-[#09090b]">{{ $detail->user->email_id ?? 'Unknown' }}</td>
          <td class="px-4 py-3 text-[#09090b]">{{ $detail->login_time ? $detail->login_time->format('d M Y H:i:s') : 'N/A' }}</td>
          <td class="px-4 py-3 text-[#09090b]">
            @if($detail->logout_time)
              {{ $detail->logout_time->format('d M Y H:i:s') }}
            @else
              @include('admin.partials.status-pill', ['label' => 'Active', 'color' => 'success'])
            @endif
          </td>
          <td class="px-4 py-3 text-[#09090b]">
            @if($detail->login_time && $detail->logout_time)
              {{ $detail->login_time->diffInMinutes($detail->logout_time) }} min
            @elseif($detail->login_time && !$detail->logout_time)
              {{ $detail->login_time->diffInMinutes(now()) }} min (active)
            @else
              -
            @endif
          </td>
          <td class="px-4 py-3">
            @if($detail->logout_time)
              @include('admin.partials.status-pill', ['label' => 'Logged Out', 'color' => 'neutral'])
            @else
              @include('admin.partials.status-pill', ['label' => 'Active', 'color' => 'success'])
            @endif
          </td>
          <td class="px-4 py-3">
            <button class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 delete-single" data-id="{{ $detail->id }}">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center py-8 text-gray-500">No login records found for the selected date range</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="border-t border-[#ebebee]">
    @include('admin.partials.pagination', [
      'paginator' => $loginDetails->appends(['start_date' => $startDate, 'end_date' => $endDate]),
      'perPage' => 20,
    ])
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Delete single record
    $(document).on('click', '.delete-single', function() {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this login record?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/login-details/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to delete record',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush
