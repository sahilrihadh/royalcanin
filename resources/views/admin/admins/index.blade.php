@extends('admin.layouts.master')

@section('title', 'Admin Users')
@section('page-title', 'Admin Users Management')

@section('breadcrumb')
<li class="breadcrumb-item active">Admin Users</li>
@endsection

@section('content')

@if(Auth::guard('admin')->user()->isSuperAdmin())
<div class="flex items-center justify-end mb-4">
  <a href="{{ route('admin.admins.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-4 py-2 rounded-lg">
    <i class="fas fa-plus"></i> Create New Admin
  </a>
</div>
@endif

<div class="bg-white border border-[#ebebee] rounded-xl overflow-hidden">
  <div class="p-4 border-b border-[#ebebee]">
    <h2 class="font-semibold text-[#09090b]">Admin Users</h2>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
      <thead>
        <tr class="bg-gray-50">
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">ID</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Username</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Full Name</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Role</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Status</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Created At</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($admins as $admin)
        <tr class="border-b border-[#ebebee] hover:bg-gray-50">
          <td class="px-4 py-3 text-[#09090b]">{{ $admin->id }}</td>
          <td class="px-4 py-3 text-[#09090b] font-medium">
            {{ $admin->username }}
            @if(Auth::guard('admin')->id() == $admin->id)
              @include('admin.partials.status-pill', ['label' => 'You', 'color' => 'info'])
            @endif
          </td>
          <td class="px-4 py-3 text-[#09090b]">{{ $admin->full_name }}</td>
          <td class="px-4 py-3">
            @php
              $roleColors = [
                'admin' => 'danger',
                'editor' => 'warning',
                'viewer' => 'info',
              ];
              $roleColor = $roleColors[$admin->user_role] ?? 'neutral';
            @endphp
            @include('admin.partials.status-pill', ['label' => ucfirst($admin->user_role), 'color' => $roleColor])
          </td>
          <td class="px-4 py-3">
            @if(Auth::guard('admin')->user()->isSuperAdmin() && Auth::guard('admin')->id() != $admin->id)
              <div class="flex items-center gap-2">
                <label class="toggle-switch align-middle">
                  <input type="checkbox" onchange="location.href='{{ route('admin.admins.toggle-status', $admin->id) }}'" {{ $admin->is_active ? 'checked' : '' }}>
                  <span class="toggle-slider"></span>
                </label>
                <span class="text-sm text-[#09090b]">{{ $admin->is_active ? 'Active' : 'Inactive' }}</span>
              </div>
            @else
              @include('admin.partials.status-pill', ['label' => $admin->is_active ? 'Active' : 'Inactive', 'color' => $admin->is_active ? 'success' : 'danger'])
            @endif
          </td>
          <td class="px-4 py-3 text-[#09090b]">{{ $admin->created_at ? $admin->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
          <td class="px-4 py-3">
            @if(Auth::guard('admin')->user()->isSuperAdmin())
              <div class="flex items-center gap-2">
                <a href="{{ route('admin.admins.edit', $admin->id) }}" class="inline-flex items-center gap-1.5 border border-[#ebebee] text-[#09090b] hover:bg-gray-50 text-xs font-semibold px-3 py-1.5 rounded-lg">
                  <i class="fas fa-edit"></i> Edit
                </a>

                @if(Auth::guard('admin')->id() != $admin->id)
                  <button type="button" class="inline-flex items-center gap-1.5 text-red-600 hover:bg-red-50 text-xs font-semibold px-3 py-1.5 rounded-lg delete-admin" data-id="{{ $admin->id }}" data-name="{{ $admin->full_name }}">
                    <i class="fas fa-trash"></i> Delete
                  </button>
                @else
                  <span class="inline-flex items-center gap-1.5 text-gray-300 text-xs font-semibold px-3 py-1.5" title="You cannot delete your own account">
                    <i class="fas fa-trash"></i> Delete
                  </span>
                @endif
              </div>
            @else
              <span class="text-gray-400 text-sm">No permissions</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center py-8 text-gray-500">No admin users found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Delete admin with SweetAlert confirmation
    $('.delete-admin').on('click', function(e) {
        e.preventDefault();
        var adminId = $(this).data('id');
        var adminName = $(this).data('name');

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete admin user "${adminName}". This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the admin user.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit delete request
                $.ajax({
                    url: `{{ route('admin.admins.index') }}/${adminId}`,
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success toaster
                            toastr.success(response.message, 'Success!');
                            // Reload page after 1.5 seconds
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            toastr.error(response.message, 'Error!');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Something went wrong!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage, 'Error!');
                    }
                });
            }
        });
    });
});
</script>
@endpush
@endsection
