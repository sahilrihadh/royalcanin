{{-- resources/views/admin/previous-sessions/show.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Session Details')
@section('page-title', 'Previous Session Details')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.previous-sessions.index') }}">Previous Sessions</a></li>
<li class="breadcrumb-item active">Session #{{ $session->id }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Session Information</h5>
      </div>
      <div class="card-body">
        <table class="table table-bordered">
          <tr>
            <th width="35%">Session ID:</th>
            <td><strong>#{{ $session->id }}</strong></td>
          </tr>
          <tr>
            <th>Participant Name:</th>
            <td><strong>{{ $session->name }}</strong></td>
          </tr>
          <tr>
            <th>Email Address:</th>
            <td>{{ $session->email_id }}</td>
          </tr>
          <tr>
            <th>Webinar Name:</th>
            <td>
              <span class="badge bg-primary">
                {{ ucfirst(str_replace('webinar', 'Webinar ', $session->session_name)) }}
              </span>
            </td>
          </tr>
          <tr>
            <th>Watched On:</th>
            <td>{{ $session->watched_on ? $session->watched_on->format('d M Y H:i:s') : 'N/A' }}</td>
          </tr>
          <tr>
            <th>Certificate Status:</th>
            <td>
              @if($session->certificate_status == 1)
                <span class="badge bg-success">Sent</span>
              @else
                <span class="badge bg-warning">Pending</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>Certificate Path:</th>
            <td>
              @if($session->certificate_path)
                <a href="{{ Storage::url($session->certificate_path) }}" target="_blank" class="btn btn-sm btn-info">
                  <i class="fas fa-download"></i> Download Certificate
                </a>
              @else
                <span class="text-muted">Not generated</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>Request Count:</th>
            <td>{{ $session->count ?? 1 }} time(s)</td>
          </tr>
          <tr>
            <th>Created At:</th>
            <td>{{ $session->created_at ? $session->created_at->format('d M Y H:i:s') : 'N/A' }}</td>
          </tr>
          <tr>
            <th>Last Updated:</th>
            <td>{{ $session->updated_at ? $session->updated_at->format('d M Y H:i:s') : 'N/A' }}</td>
          </tr>
        </table>
        
        <div class="mt-3">
          <a href="{{ route('admin.previous-sessions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
          </a>
          @if($session->certificate_status != 1)
            <button class="btn btn-primary resend-certificate" data-id="{{ $session->id }}">
              <i class="fas fa-envelope"></i> Send Certificate
            </button>
          @endif
          <button class="btn btn-danger delete-single" data-id="{{ $session->id }}">
            <i class="fas fa-trash"></i> Delete Record
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Certificate Preview</h5>
      </div>
      <div class="card-body text-center">
        @if($session->certificate_path && file_exists(storage_path('app/public/' . $session->certificate_path)))
          <img src="{{ Storage::url($session->certificate_path) }}" alt="Certificate" class="img-fluid img-thumbnail" style="max-height: 400px;">
          <div class="mt-3">
            <a href="{{ Storage::url($session->certificate_path) }}" download class="btn btn-success">
              <i class="fas fa-download"></i> Download Certificate
            </a>
          </div>
        @else
          <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Certificate not generated yet.
            @if($session->certificate_status != 1)
              <br>Click "Send Certificate" to generate and send.
            @endif
          </div>
          @if($session->certificate_status != 1)
            <div class="mt-3">
              <button class="btn btn-primary resend-certificate" data-id="{{ $session->id }}">
                <i class="fas fa-envelope"></i> Generate & Send Certificate
              </button>
            </div>
          @endif
        @endif
      </div>
    </div>
    
    <div class="card mt-3">
      <div class="card-header">
        <h5 class="mb-0">Activity Log</h5>
      </div>
      <div class="card-body">
        <ul class="list-group">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Certificate Requested
            <span class="badge bg-primary rounded-pill">{{ $session->created_at ? $session->created_at->diffForHumans() : 'N/A' }}</span>
          </li>
          @if($session->certificate_status == 1)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Certificate Sent
            <span class="badge bg-success rounded-pill">{{ $session->updated_at ? $session->updated_at->diffForHumans() : 'N/A' }}</span>
          </li>
          @endif
          @if($session->count && $session->count > 1)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Total Requests
            <span class="badge bg-info rounded-pill">{{ $session->count }} times</span>
          </li>
          @endif
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Resend Certificate
    $('.resend-certificate').on('click', function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Send Certificate?',
            text: "This will generate and send the certificate to " + "{{ $session->email_id }}",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, send it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Generating and sending certificate...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: '/admin/previous-sessions/' + id + '/resend-certificate',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sent!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        var message = xhr.responseJSON?.message || 'Failed to send certificate';
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
    
    // Delete single record
    $('.delete-single').on('click', function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this session record? This action cannot be undone!",
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
                                window.location.href = '{{ route("admin.previous-sessions.index") }}';
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