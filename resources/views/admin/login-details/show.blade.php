{{-- resources/views/admin/login-details/show.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Login Detail')
@section('page-title', 'Login Session Details')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.login-details.index') }}">Login Details</a></li>
<li class="breadcrumb-item active">Session #{{ $loginDetail->id }}</li>
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
            <th width="30%">Session ID:</th>
            <td>{{ $loginDetail->id }}</td>
          </tr>
          <tr>
            <th>User Name:</th>
            <td><strong>{{ $loginDetail->user->full_name ?? 'Unknown' }}</strong></td>
          </tr>
          <tr>
            <th>Email:</th>
            <td>{{ $loginDetail->user->email_id ?? 'Unknown' }}</td>
          </tr>
          <tr>
            <th>Login Time:</th>
            <td>{{ $loginDetail->login_time ? $loginDetail->login_time->format('d M Y H:i:s') : 'N/A' }}</td>
          </tr>
          <tr>
            <th>Logout Time:</th>
            <td>
              @if($loginDetail->logout_time)
                {{ $loginDetail->logout_time->format('d M Y H:i:s') }}
              @else
                <span class="badge bg-success">Still Active</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>Duration:</th>
            <td>
              @if($loginDetail->login_time && $loginDetail->logout_time)
                <strong>{{ $loginDetail->login_time->diffInMinutes($loginDetail->logout_time) }} minutes</strong>
                ({{ floor($loginDetail->login_time->diffInMinutes($loginDetail->logout_time) / 60) }} hours {{ $loginDetail->login_time->diffInMinutes($loginDetail->logout_time) % 60 }} minutes)
              @elseif($loginDetail->login_time && !$loginDetail->logout_time)
                <strong>{{ $loginDetail->login_time->diffInMinutes(now()) }} minutes</strong> (active)
              @else
                -
              @endif
            </td>
          </tr>
          <tr>
            <th>IP Address:</th>
            <td>{{ $loginDetail->ip_address ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>User Agent:</th>
            <td><small>{{ $loginDetail->user_agent ?? 'N/A' }}</small></td>
          </tr>
          <tr>
            <th>Created At:</th>
            <td>{{ $loginDetail->created_at ? $loginDetail->created_at->format('d M Y H:i:s') : 'N/A' }}</td>
          </tr>
        </table>
        
        <div class="mt-3">
          <a href="{{ route('admin.login-details.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
          </a>
          <button class="btn btn-danger delete-single" data-id="{{ $loginDetail->id }}">
            <i class="fas fa-trash"></i> Delete Record
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">User Information</h5>
      </div>
      <div class="card-body">
        <table class="table table-bordered">
          <tr>
            <th width="35%">Full Name:</th>
            <td>{{ $loginDetail->user->full_name ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Mobile Number:</th>
            <td>{{ $loginDetail->user->mobile_number ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Email:</th>
            <td>{{ $loginDetail->user->email_id ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Clinic Name:</th>
            <td>{{ $loginDetail->user->clinic_name ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>City:</th>
            <td>{{ $loginDetail->user->city ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>State:</th>
            <td>{{ $loginDetail->user->state ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Registration Number:</th>
            <td>{{ $loginDetail->user->registration_number ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Last Seen:</th>
            <td>{{ $loginDetail->user->last_seen_at ? \Carbon\Carbon::parse($loginDetail->user->last_seen_at)->diffForHumans() : 'Never' }}</td>
          </tr>
        </table>
      </div>
    </div>
    
    <div class="card mt-3">
      <div class="card-header">
        <h5 class="mb-0">Previous Sessions (Last 5)</h5>
      </div>
      <div class="card-body">
        @if($previousLogins->count() > 0)
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Login Time</th>
                  <th>Logout Time</th>
                  <th>Duration</th>
                </tr>
              </thead>
              <tbody>
                @foreach($previousLogins as $login)
                <tr>
                  <td>{{ $login->login_time ? $login->login_time->format('d M Y H:i') : 'N/A' }}</td>
                  <td>{{ $login->logout_time ? $login->logout_time->format('d M Y H:i') : 'Active' }}</td>
                  <td>
                    @if($login->login_time && $login->logout_time)
                      {{ $login->login_time->diffInMinutes($login->logout_time) }} min
                    @elseif($login->login_time && !$login->logout_time)
                      {{ $login->login_time->diffInMinutes(now()) }} min
                    @else
                      -
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-muted text-center">No previous sessions found</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Delete single record
    $('.delete-single').on('click', function() {
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
                                window.location.href = '{{ route("admin.login-details.index") }}';
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