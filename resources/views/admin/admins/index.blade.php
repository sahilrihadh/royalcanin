@extends('admin.layouts.master')

@section('title', 'Admin Users')
@section('page-title', 'Admin Users Management')

@section('content')
<div class="card">
  <div class="card-header">
    <h5>Admin Users</h5>
    @if(Auth::guard('admin')->user()->isSuperAdmin())
    <a href="{{ route('admin.admins.create') }}" class="btn btn-primary btn-sm float-end">Create New Admin</a>
    @endif
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Full Name</th>
            <th>Role</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($admins as $admin)
          <tr>
            <td>{{ $admin->id }}</td>
            <td>{{ $admin->username }} @if(Auth::guard('admin')->id() == $admin->id)<span class="badge bg-primary">You</span>@endif</td>
            <td>{{ $admin->full_name }}</td>
            <td>
              @php
                $roleColors = [
                  'admin' => 'danger',
                  'editor' => 'warning',
                  'viewer' => 'info'
                ];
                $color = $roleColors[$admin->user_role] ?? 'secondary';
              @endphp
              <span class="badge bg-{{ $color }}">
                {{ ucfirst($admin->user_role) }}
              </span>
            </td>
            <td>
              @if($admin->is_active)
                <span class="badge bg-success">Active</span>
              @else
                <span class="badge bg-danger">Inactive</span>
              @endif
            </td>
            <td>{{ $admin->created_at ? $admin->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
            <td>
              @if(Auth::guard('admin')->user()->isSuperAdmin())
                <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-info btn-sm">Edit</a>
                
                <a href="{{ route('admin.admins.toggle-status', $admin->id) }}" 
                   class="btn btn-{{ $admin->is_active ? 'warning' : 'success' }} btn-sm">
                  {{ $admin->is_active ? 'Deactivate' : 'Activate' }}
                </a>
                
                @if(Auth::guard('admin')->id() != $admin->id)
                  <button type="button" class="btn btn-danger btn-sm delete-admin" data-id="{{ $admin->id }}" data-name="{{ $admin->full_name }}">
                    Delete
                  </button>
                @else
                  <button type="button" class="btn btn-danger btn-sm" disabled title="You cannot delete your own account">
                    Delete
                  </button>
                @endif
              @else
                <span class="text-muted">No permissions</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center">No admin users found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
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