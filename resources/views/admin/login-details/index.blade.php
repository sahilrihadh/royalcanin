@extends('admin.layouts.master')

@section('title', 'Login Details')
@section('page-title', 'User Login Details')

@section('breadcrumb')
<li class="breadcrumb-item active">Login Details</li>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Login History</h5>
  </div>
  <div class="card-body">
    <!-- Date Filter -->
    <form method="GET" action="{{ route('admin.login-details.index') }}" class="mb-4">
      <div class="row align-items-end">
        <div class="col-md-3">
          <label class="form-label">Start Date</label>
          <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">End Date</label>
          <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        <div class="col-md-6">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filter
          </button>
          <a href="{{ route('admin.login-details.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export Excel
          </a>
          <a href="{{ route('admin.login-details.index') }}" class="btn btn-secondary">
            <i class="fas fa-sync"></i> Reset
          </a>
          <a href="{{ route('admin.login-details.attendance') }}" class="btn btn-dark">
            <i class="fas fa-sync"></i> Unique Attendance
          </a>
        </div>
      </div>
    </form>

    <!-- Login Details Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>User</th>
            <th>Email</th>
            <th>Login Time</th>
            <th>Logout Time</th>
            <th>Duration</th>
            <th>Status</th>
            <th width="100">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($loginDetails as $index => $detail)
          <tr>
            <td>{{ $loginDetails->firstItem() + $index }}</td>
            <td>{{ $detail->user->full_name ?? 'Unknown' }}</td>
            <td>{{ $detail->user->email_id ?? 'Unknown' }}</td>
            <td>{{ $detail->login_time ? $detail->login_time->format('d M Y H:i:s') : 'N/A' }}</td>
            <td>
              @if($detail->logout_time)
                {{ $detail->logout_time->format('d M Y H:i:s') }}
              @else
                <span class="badge bg-success">Active</span>
              @endif
            </td>
            <td>
              @if($detail->login_time && $detail->logout_time)
                {{ $detail->login_time->diffInMinutes($detail->logout_time) }} min
              @elseif($detail->login_time && !$detail->logout_time)
                {{ $detail->login_time->diffInMinutes(now()) }} min (active)
              @else
                -
              @endif
            </td>
            <td>
              @if($detail->logout_time)
                <span class="badge bg-secondary">Logged Out</span>
              @else
                <span class="badge bg-success">Active</span>
              @endif
            </td>
            <td>
              <button class="btn btn-sm btn-danger delete-single" data-id="{{ $detail->id }}">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center">No login records found for the selected date range</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
      {{ $loginDetails->appends(['start_date' => $startDate, 'end_date' => $endDate])->links() }}
    </div>
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