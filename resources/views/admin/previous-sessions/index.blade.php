{{-- resources/views/admin/previous-sessions/index.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Previous Sessions')
@section('page-title', 'Previous Sessions & Certificates')

@section('breadcrumb')
<li class="breadcrumb-item active">Previous Sessions</li>
@endsection

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h5 class="mb-0">Session Participants</h5>
    <div>
      <a href="{{ route('admin.previous-sessions.export', ['session_name' => $sessionName]) }}" class="btn btn-sm btn-success">
        <i class="fas fa-file-excel"></i> Export Excel
      </a>
    </div>
  </div>
  <div class="card-body">

    <!-- Webinar filter buttons -->
    <div class="mb-4 d-flex flex-wrap gap-2">
      <a href="{{ route('admin.previous-sessions.index') }}"
         class="btn btn-sm {{ !$sessionName ? 'btn-primary' : 'btn-outline-primary' }}">
        All
      </a>
      @foreach($webinars as $webinar)
      <a href="{{ route('admin.previous-sessions.index', ['session_name' => $webinar]) }}"
         class="btn btn-sm {{ $sessionName === $webinar ? 'btn-primary' : 'btn-outline-primary' }}">
        {{ ucfirst($webinar) }}
      </a>
      @endforeach
    </div>

    <!-- Sessions Table -->
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Session Participants List</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
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
                <td colspan="7" class="text-center">No sessions found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
      {{ $sessions->appends(['session_name' => $sessionName])->links() }}
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
});
</script>
@endpush