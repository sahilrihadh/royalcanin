{{-- resources/views/admin/previous-sessions/index.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Previous Sessions')
@section('page-title', 'Previous Sessions & Certificates')

@section('breadcrumb')
<li class="breadcrumb-item active">Previous Sessions</li>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Session Participants</h5>
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
    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card bg-primary text-white">
          <div class="card-body">
            <h5 class="card-title">Total Participants</h5>
            <h3 class="mb-0">{{ $totalSessions }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-success text-white">
          <div class="card-body">
            <h5 class="card-title">Certificates Sent</h5>
            <h3 class="mb-0">{{ $certificatesSent }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-info text-white">
          <div class="card-body">
            <h5 class="card-title">Unique Users</h5>
            <h3 class="mb-0">{{ $uniqueUsers }}</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Webinar Statistics -->
    <div class="card mb-4">
      <div class="card-header">
        <h6 class="mb-0">Webinar Statistics</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Webinar</th>
                <th>Total Participants</th>
                <th>Certificates Sent</th>
                <th>Certificate Rate</th>
              </tr>
            </thead>
            <tbody>
              @foreach($webinarStats as $stat)
              <tr>
                <td>{{ ucfirst($stat->session_name) }}</td>
                <td>{{ $stat->total }}</td>
                <td>{{ $stat->certificates_sent }}</td>
                <td>
                  <div class="progress">
                    <div class="progress-bar" style="width: {{ $stat->total > 0 ? round(($stat->certificates_sent / $stat->total) * 100) : 0 }}%">
                      {{ $stat->total > 0 ? round(($stat->certificates_sent / $stat->total) * 100) : 0 }}%
                    </div>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
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

    <!-- Sessions Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th width="50">
              <input type="checkbox" id="selectAllCheckbox">
            </th>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Webinar</th>
            <th>Watched On</th>
            <th>Certificate</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sessions as $session)
          <tr>
            <td>
              <input type="checkbox" class="record-checkbox" value="{{ $session->id }}">
            </td>
            <td>{{ $session->id }}</td>
            <td>{{ $session->name }}</td>
            <td>{{ $session->email_id }}</td>
            <td>{{ ucfirst($session->session_name) }}</td>
            <td>{{ $session->watched_on ? $session->watched_on->format('d M Y H:i') : 'N/A' }}</td>
            <td>
              @if($session->certificate_status == 1)
                <span class="badge bg-success">Sent</span>
              @else
                <span class="badge bg-warning">Pending</span>
              @endif
            </td>
            <td>
              <a href="{{ route('admin.previous-sessions.show', $session->id) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i>
              </a>
              @if($session->certificate_status != 1)
                <button class="btn btn-sm btn-primary resend-certificate" data-id="{{ $session->id }}">
                  <i class="fas fa-envelope"></i>
                </button>
              @endif
              <button class="btn btn-sm btn-danger delete-single" data-id="{{ $session->id }}">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center">No sessions found</td>
          </tr>
          @endforelse
        </tbody>
       </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
      {{ $sessions->links() }}
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
    
    // Resend Certificate
    $('.resend-certificate').on('click', function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Resend Certificate?',
            text: "This will resend the certificate to the participant's email.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, resend it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/previous-sessions/' + id + '/resend-certificate',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sent!',
                            text: 'Certificate resent successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to resend certificate',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
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
                    url: '{{ route("admin.previous-sessions.bulk-delete") }}',
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
            text: "You want to delete this session record?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/previous-sessions/' + id,
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
                    url: '{{ route("admin.previous-sessions.clear-old") }}',
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