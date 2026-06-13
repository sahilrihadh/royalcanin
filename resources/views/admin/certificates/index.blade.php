@extends('admin.layouts.master')

@section('title', 'Certificates')
@section('page-title', 'Certificates Management')

@section('content')
<div class="card">
  <div class="card-header">
    <h5>Certificate Records</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>User Name</th>
            <th>Email</th>
            <th>Webinar</th>
            <th>Certificate Status</th>
            <th>Issued On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="7" class="text-center">Loading...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection