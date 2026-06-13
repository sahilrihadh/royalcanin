@extends('admin.layouts.master')

@section('title', 'Users')
@section('page-title', 'Users Management')

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card bg-primary text-white h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="mb-0">Total Users</h6>
            <h2 class="mb-0">{{ $totalUsers }}</h2>
          </div>
          <i class="fas fa-users fa-2x"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card bg-success text-white">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="mb-0">Online Users</h6>
            <h2 class="mb-0">{{ $onlineUsers }}</h2>
            <small>Active in last 5 min</small>
          </div>
          <i class="fas fa-circle fa-2x" style="font-size: 2rem;"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5>All Users</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>City</th>
            <th>State</th>
            <th>Status</th>
            <th>Registered On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
          @php
          $onlineThreshold = \Carbon\Carbon::now()->subMinutes(5);
          $isOnline = $user->last_seen_at && $user->last_seen_at >= $onlineThreshold;
          @endphp
          <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->full_name }}</td>
            <td>{{ $user->email_id }}</td>
            <td>{{ $user->mobile_number }}</td>
            <td>{{ $user->city ?? '-' }}</td>
            <td>{{ $user->state ?? '-' }}</td>
            <td>
              @if($isOnline)
              <span class="badge bg-success">
                <i class="fas fa-circle" style="font-size: 8px;"></i> Online
              </span>
              @else
              <span class="badge bg-secondary">Offline</span>
              @endif
            </td>
            <td>{{ $user->created_at->format('d M Y') }}</td>
            <td>
              @if(!$isOnline)
              <button class="btn btn-sm btn-danger delete-user"
                data-id="{{ $user->id }}"
                data-name="{{ $user->full_name }}">
                <i class="fas fa-trash"></i> Delete
              </button>
              @else
              <button class="btn btn-sm btn-secondary" disabled title="Cannot delete online user">
                <i class="fas fa-trash"></i> Delete
              </button>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center">No users found</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
      {{ $users->links() }}
    </div>
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

      swal({
        title: "Are you sure?",
        text: "You want to delete user: " + userName + "?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel!",
        closeOnConfirm: false,
        closeOnCancel: false
      }, function(isConfirm) {
        if (isConfirm) {
          $.ajax({
            url: '/admin/users/' + userId,
            type: 'DELETE',
            data: {
              _token: '{{ csrf_token() }}'
            },
            success: function(response) {
              if (response.success) {
                swal({
                  title: "Deleted!",
                  text: response.message,
                  type: "success",
                  timer: 2000,
                  showConfirmButton: false
                });

                // Remove row from table
                $('button[data-id="' + userId + '"]').closest('tr').fadeOut();

                // Update online users count
                showToast(response.message, 'success');
              }
            },
            error: function(xhr) {
              var message = xhr.responseJSON?.message || "Failed to delete user";
              swal({
                title: "Error!",
                text: message,
                type: "error",
                timer: 2000,
                showConfirmButton: false
              });
              showToast(message, 'error');
            }
          });
        } else {
          swal({
            title: "Cancelled",
            text: "User deletion cancelled",
            type: "error",
            timer: 1500,
            showConfirmButton: false
          });
        }
      });
    });

    // Toast notification function
    function showToast(message, type) {
      var bgColor = type === 'success' ? '#28a745' : '#dc3545';
      var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

      var toastHtml = `
                <div class="toast-notification" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; background: ${bgColor}; color: white; padding: 12px 20px; border-radius: 5px; animation: slideIn 0.3s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                    <i class="fas ${icon}"></i>
                    ${message}
                </div>
            `;

      $('body').append(toastHtml);

      setTimeout(function() {
        $('.toast-notification').fadeOut(300, function() {
          $(this).remove();
        });
      }, 3000);
    }
  });
</script>

<style>
  @keyframes slideIn {
    from {
      transform: translateX(100%);
      opacity: 0;
    }

    to {
      transform: translateX(0);
      opacity: 1;
    }
  }

  .table-hover tbody tr:hover {
    background-color: #f5f5f5;
  }

  .badge .fa-circle {
    font-size: 8px;
    vertical-align: middle;
  }

  .btn:disabled {
    cursor: not-allowed;
    opacity: 0.6;
  }
</style>
@endpush