{{-- resources/views/admin/login-details/index.blade.php --}}
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
    <div class="float-end">
      <button class="btn btn-sm btn-danger" id="bulkDeleteBtn" style="display:none;">
        <i class="fas fa-trash"></i> Delete Selected
      </button>
      <button class="btn btn-sm btn-warning" id="clearOldBtn">
        <i class="fas fa-clock"></i> Clear Old Records
      </button>
    </div>
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
        <div class="col-md-3">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filter
          </button>
          <a href="{{ route('admin.login-details.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success">
            <i class="fas fa-download"></i> Export CSV
          </a>
        </div>
      </div>
    </form>

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card bg-primary text-white">
          <div class="card-body">
            <h5 class="card-title">Total Logins</h5>
            <h3 class="mb-0">{{ $totalLogins }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-success text-white">
          <div class="card-body">
            <h5 class="card-title">Unique Users</h5>
            <h3 class="mb-0">{{ $uniqueUsers }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-info text-white">
          <div class="card-body">
            <h5 class="card-title">Avg Duration</h5>
            <h3 class="mb-0">{{ round($averageDuration ?? 0) }} min</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-warning text-white">
          <div class="card-body">
            <h5 class="card-title">Peak Hour</h5>
            <h3 class="mb-0">{{ $peakHours ? sprintf('%02d:00', $peakHours->hour) : 'N/A' }}</h3>
            <small>{{ $peakHours ? $peakHours->count . ' logins' : '' }}</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulkActionsBar" class="alert alert-info" style="display:none;">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <i class="fas fa-check-circle"></i> <span id="selectedCount">0</span> records selected
        </div>
        <div>
          <button class="btn btn-sm btn-success" id="selectAllBtn">Select All</button>
          <button class="btn btn-sm btn-secondary" id="clearSelectionBtn">Clear</button>
          <button class="btn btn-sm btn-danger" id="confirmBulkDelete">
            <i class="fas fa-trash"></i> Delete Selected
          </button>
        </div>
      </div>
    </div>

    <!-- Login Details Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th width="50">
              <input type="checkbox" id="selectAllCheckbox">
            </th>
            <th>ID</th>
            <th>User</th>
            <th>Email</th>
            <th>Login Time</th>
            <th>Logout Time</th>
            <th>Duration</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($loginDetails as $detail)
          <tr>
            <td>
              <input type="checkbox" class="record-checkbox" value="{{ $detail->id }}">
            </td>
            <td>{{ $detail->id }}</td>
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
                {{ $detail->login_time->diffInMinutes($detail->logout_time) }} minutes
              @elseif($detail->login_time && !$detail->logout_time)
                {{ $detail->login_time->diffInMinutes(now()) }} minutes (active)
              @else
                -
              @endif
            </td>
            <td>
              <a href="{{ route('admin.login-details.show', $detail->id) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i>
              </a>
              <button class="btn btn-sm btn-danger delete-single" data-id="{{ $detail->id }}">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center">No login records found</td>
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
    let selectedIds = [];
    
    // Select All checkbox functionality
    $('#selectAllCheckbox').on('change', function() {
        $('.record-checkbox').prop('checked', $(this).is(':checked'));
        updateSelectedRecords();
    });
    
    // Individual checkbox change
    $(document).on('change', '.record-checkbox', function() {
        updateSelectedRecords();
    });
    
    // Update selected records
    function updateSelectedRecords() {
        selectedIds = [];
        $('.record-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        if (selectedIds.length > 0) {
            $('#bulkActionsBar').show();
            $('#selectedCount').text(selectedIds.length);
            $('#bulkDeleteBtn').show();
        } else {
            $('#bulkActionsBar').hide();
            $('#bulkDeleteBtn').hide();
        }
        
        // Update select all checkbox
        var totalCheckboxes = $('.record-checkbox').length;
        var checkedCheckboxes = $('.record-checkbox:checked').length;
        $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes);
    }
    
    // Select All button
    $('#selectAllBtn').on('click', function() {
        $('.record-checkbox').prop('checked', true);
        updateSelectedRecords();
    });
    
    // Clear Selection button
    $('#clearSelectionBtn').on('click', function() {
        $('.record-checkbox').prop('checked', false);
        updateSelectedRecords();
    });
    
    // Bulk Delete
    $('#confirmBulkDelete, #bulkDeleteBtn').on('click', function() {
        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Selection',
                text: 'Please select records to delete',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }
        
        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${selectedIds.length} record(s). This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.login-details.bulk-delete") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
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
                            text: 'Failed to delete records',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    });
    
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
    
    // Clear old records
    $('#clearOldBtn').on('click', function() {
        Swal.fire({
            title: 'Clear Old Records?',
            text: "Enter number of days to keep (records older than this will be deleted):",
            input: 'number',
            inputValue: 30,
            inputAttributes: {
                min: 1,
                max: 365
            },
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.ajax({
                    url: '{{ route("admin.login-details.clear-old") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        days: result.value
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
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
                            text: 'Failed to clear records',
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