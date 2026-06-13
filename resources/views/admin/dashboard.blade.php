@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-4">
  <h1 class="mt-4">Dashboard</h1>

  <div class="row">
    <div class="col-xl-3 col-md-6">
      <div class="card bg-primary text-white mb-4">
        <div class="card-body">
          <h3>{{ $totalUsers }}</h3>
          <p>Total Users</p>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card bg-success text-white mb-4">
        <div class="card-body">
          <h3>{{ $totalWebinars }}</h3>
          <p>Total Webinars</p>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card bg-warning text-white mb-4">
        <div class="card-body">
          <h3>{{ $totalPolls }}</h3>
          <p>Total Polls</p>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card bg-danger text-white mb-4">
        <div class="card-body">
          <h3>{{ $totalCertificates }}</h3>
          <p>Certificates Issued</p>
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
                <td>{{ $user->created_at->format('d M Y') }}</td>
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
                <td>{{ $activity->watched_on ? \Carbon\Carbon::parse($activity->watched_on)->format('d M Y H:i') : 'N/A' }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection