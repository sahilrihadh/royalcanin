@extends('admin.layouts.master')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
  <div class="row">
    <div class="col-xl-3 col-md-6">
      <div class="kpi-card kpi-primary mb-4">
        <div class="card-body">
          <div class="kpi-icon"><i class="fas fa-users"></i></div>
          <div class="kpi-value">{{ $totalUsers }}</div>
          <div class="kpi-label">Total Users</div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="kpi-card kpi-success mb-4">
        <div class="card-body">
          <div class="kpi-icon"><i class="fas fa-chalkboard-teacher"></i></div>
          <div class="kpi-value">{{ $totalWebinars }}</div>
          <div class="kpi-label">Total Webinars</div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="kpi-card kpi-warning mb-4">
        <div class="card-body">
          <div class="kpi-icon"><i class="fas fa-poll"></i></div>
          <div class="kpi-value">{{ $totalPolls }}</div>
          <div class="kpi-label">Total Polls</div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="kpi-card kpi-danger mb-4">
        <div class="card-body">
          <div class="kpi-icon"><i class="fas fa-certificate"></i></div>
          <div class="kpi-value">{{ $totalCertificates }}</div>
          <div class="kpi-label">Certificates Issued</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-6">
      <div class="card mb-4">
        <div class="card-header">
          <i class="fas fa-users me-1"></i>
          Recent Users
        </div>
        <div class="card-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Registered On</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentUsers as $user)
              <tr>
                <td>{{ $user->full_name }}</td>
                <td>{{ $user->email_id }}</td>
                <td>{{ $user->created_at?->format('d M Y') ?? 'N/A' }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-xl-6">
      <div class="card mb-4">
        <div class="card-header">
          <i class="fas fa-clock me-1"></i>
          Recent Activities
        </div>
        <div class="card-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Name</th>
                <th>Webinar</th>
                <th>Watched On</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentActivities as $activity)
              <tr>
                <td>{{ $activity->name ?? 'N/A' }}</td>
                <td>{{ $activity->session_name }}</td>
                <td>{{ isset($activity->watched_on) ? \Carbon\Carbon::parse($activity->watched_on)->format('d M Y H:i') : 'N/A' }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection