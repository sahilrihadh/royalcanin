@extends('admin.layouts.master')

@section('title', 'Daily Attendance')
@section('page-title', 'Daily Attendance')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.login-details.index') }}">Login Details</a></li>
<li class="breadcrumb-item active">Attendance</li>
@endsection

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Daily Attendance <small class="text-muted">— one row per user per day</small></h5>
    <a href="{{ route('admin.login-details.index') }}" class="btn btn-sm btn-outline-secondary">
      <i class="fas fa-list"></i> All Sessions
    </a>
  </div>
  <div class="card-body">
    <!-- Date Filter -->
    <form method="GET" action="{{ route('admin.login-details.attendance') }}" class="mb-4">
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
          <a href="{{ route('admin.login-details.export-attendance', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export Excel
          </a>
          <a href="{{ route('admin.login-details.attendance') }}" class="btn btn-secondary">
            <i class="fas fa-sync"></i> Reset
          </a>
        </div>
      </div>
    </form>

    <!-- Attendance Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>User</th>
            <th>Email</th>
            <th>Date</th>
            <th>Sessions</th>
            <th>First Login</th>
            <th>Last Activity</th>
            <th>Total Duration</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($attendance as $index => $detail)
          <tr>
            <td>{{ $attendance->firstItem() + $index }}</td>
            <td>{{ $detail->user->full_name ?? 'Unknown' }}</td>
            <td>{{ $detail->user->email_id ?? 'Unknown' }}</td>
            <td>{{ \Carbon\Carbon::parse($detail->date)->format('d M Y') }}</td>
            <td>{{ $detail->session_count }}</td>
            <td>{{ $detail->first_login ? $detail->first_login->format('h:i A') : 'N/A' }}</td>
            <td>
              @if($detail->last_activity)
                {{ $detail->last_activity->format('h:i A') }}
              @else
                <span class="badge bg-success">Still active</span>
              @endif
            </td>
            <td>{{ $detail->total_duration }}</td>
            <td>
              @if($detail->is_active)
                <span class="badge bg-success">Active</span>
              @else
                <span class="badge bg-secondary">Logged Out</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center">No login records found for the selected date range</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
      {{ $attendance->appends(['start_date' => $startDate, 'end_date' => $endDate])->links() }}
    </div>
  </div>
</div>
@endsection