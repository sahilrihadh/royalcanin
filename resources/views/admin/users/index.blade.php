@extends('admin.layouts.master')

@section('title', 'Users')
@section('page-title', 'Users Management')

@section('breadcrumb')
<li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="flex flex-wrap gap-3 mb-6">
  @include('admin.partials.stat-card', ['icon' => 'fa-users', 'value' => $totalUsers, 'label' => 'Total Users'])
  @include('admin.partials.stat-card', ['icon' => 'fa-user-check', 'value' => $onlineUsers, 'label' => 'Online (5 min)'])
</div>

<div class="bg-white border border-[#ebebee] rounded-xl overflow-hidden">
  <form method="GET" action="{{ route('admin.users') }}">
    <input type="hidden" name="per_page" value="{{ $perPage }}">

    <div class="flex flex-wrap items-center justify-between gap-3 p-4 border-b border-[#ebebee]">
      <div class="font-semibold text-[#09090b] whitespace-nowrap">
        Showing {{ $users->count() }} of {{ $users->total() }} users
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <div class="relative">
          <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
          <input type="text" name="search" placeholder="Search users..." value="{{ $search }}"
                 class="pl-9 pr-3 py-2 text-sm border border-[#ebebee] rounded-lg min-w-[220px] text-[#09090b] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
        </div>

        <select name="status" onchange="this.form.submit()"
                class="text-sm border border-[#ebebee] rounded-lg px-3 py-2 min-w-[150px] text-[#09090b] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
          <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All statuses</option>
          <option value="online" {{ $status === 'online' ? 'selected' : '' }}>Online</option>
          <option value="offline" {{ $status === 'offline' ? 'selected' : '' }}>Offline</option>
        </select>

        <select name="sort" onchange="this.form.submit()"
                class="text-sm border border-[#ebebee] rounded-lg px-3 py-2 min-w-[150px] text-[#09090b] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
          <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest first</option>
          <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest first</option>
          <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
          <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
        </select>

        <button type="submit" class="inline-flex items-center gap-2 border border-[#ebebee] text-[#09090b] hover:bg-gray-50 font-semibold text-sm px-3 py-2 rounded-lg">
          <i class="fas fa-filter"></i> Filters
        </button>

        <a href="{{ route('admin.users.export') }}" class="inline-flex items-center gap-2 border border-[#ebebee] text-[#09090b] hover:bg-gray-50 font-semibold text-sm px-3 py-2 rounded-lg">
          <i class="fas fa-file-excel"></i> Export
        </a>
      </div>
    </div>
  </form>

  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
      <thead>
        <tr class="bg-gray-50">
          <th class="px-4 py-3 w-8"><input type="checkbox" class="rounded border-gray-300"></th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Member</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Clinic</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Location</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Status</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Registered</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Activity</th>
          <th class="px-4 py-3 w-12"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        @php
        $onlineThreshold = \Carbon\Carbon::now()->subMinutes(5);
        $isOnline = $user->last_seen_at && $user->last_seen_at >= $onlineThreshold;
        $location = trim(collect([$user->city, $user->state])->filter()->implode(', '));
        @endphp
        <tr class="border-b border-[#ebebee] hover:bg-gray-50">
          <td class="px-4 py-3"><input type="checkbox" class="rounded border-gray-300"></td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-3">
              @include('admin.partials.avatar', ['name' => $user->full_name])
              <div>
                <div class="font-semibold text-[#09090b]">{{ $user->full_name }}</div>
                <div class="text-gray-500 text-xs">{{ $user->email_id }}</div>
              </div>
            </div>
          </td>
          <td class="px-4 py-3 text-[#09090b]">{{ $user->clinic_name ?? '-' }}</td>
          <td class="px-4 py-3 text-[#09090b]">
            @if($location !== '')
              <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>{{ $location }}
            @else
              <span class="text-gray-400">-</span>
            @endif
          </td>
          <td class="px-4 py-3">
            @if($isOnline)
              @include('admin.partials.status-pill', ['label' => 'Online', 'color' => 'success'])
            @else
              @include('admin.partials.status-pill', ['label' => 'Offline', 'color' => 'neutral'])
            @endif
          </td>
          <td class="px-4 py-3 text-[#09090b]">{{ $user->created_at?->format('d M Y') ?? 'N/A' }}</td>
          <td class="px-4 py-3 text-[#09090b]">
            @if($isOnline)
              Current session
            @else
              {{ $user->last_seen_at?->diffForHumans() ?? 'Never' }}
            @endif
          </td>
          <td class="px-4 py-3">
            <div class="dropdown">
              <button class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-ellipsis-v"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-[#ebebee] rounded-lg py-1">
                <li>
                  @if(!$isOnline)
                  <button class="dropdown-item text-red-600 hover:bg-red-50 text-sm px-3 py-2 w-full text-left delete-user" data-id="{{ $user->id }}" data-name="{{ $user->full_name }}">
                    <i class="fas fa-trash me-2"></i>Delete
                  </button>
                  @else
                  <span class="dropdown-item text-gray-400 text-sm px-3 py-2" title="Cannot delete online user">
                    <i class="fas fa-trash me-2"></i>Delete
                  </span>
                  @endif
                </li>
              </ul>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center py-8 text-gray-500">No users found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="border-t border-[#ebebee]">
    @include('admin.partials.pagination', ['paginator' => $users, 'perPage' => $perPage])
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    // Delete user with SweetAlert
    $('.delete-user').on('click', function(e) {
      e.preventDefault();

      var userId = $(this).data('id');
      var userName = $(this).data('name');

      Swal.fire({
        title: 'Are you sure?',
        text: "You want to delete user: " + userName + "?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '/admin/users/' + userId,
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
            error: function(xhr) {
              var message = xhr.responseJSON?.message || "Failed to delete user";
              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
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
