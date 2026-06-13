@extends('admin.layouts.master')

@section('title', 'Create Admin User')
@section('page-title', 'Create New Admin User')

@section('content')
<div class="card">
  <div class="card-header">
    <h5>Create Admin User</h5>
    <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary btn-sm float-end">Back to List</a>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.admins.store') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
        <input type="text" 
               class="form-control @error('username') is-invalid @enderror" 
               id="username" 
               name="username" 
               value="{{ old('username') }}" 
               required>
        @error('username')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" 
               class="form-control @error('full_name') is-invalid @enderror" 
               id="full_name" 
               name="full_name" 
               value="{{ old('full_name') }}" 
               required>
        @error('full_name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
        <input type="password" 
               class="form-control @error('password') is-invalid @enderror" 
               id="password" 
               name="password" 
               required>
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Minimum 6 characters</small>
      </div>

      <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
        <input type="password" 
               class="form-control" 
               id="password_confirmation" 
               name="password_confirmation" 
               required>
      </div>

      <div class="mb-3">
        <label for="user_role" class="form-label">User Role <span class="text-danger">*</span></label>
        <select class="form-control @error('user_role') is-invalid @enderror" 
                id="user_role" 
                name="user_role" 
                required>
          <option value="">Select Role</option>
          <option value="admin" {{ old('user_role') == 'admin' ? 'selected' : '' }}>Administrator (Full Access)</option>
          <option value="editor" {{ old('user_role') == 'editor' ? 'selected' : '' }}>Editor (Limited Access)</option>
          <option value="viewer" {{ old('user_role') == 'viewer' ? 'selected' : '' }}>Viewer (Read Only)</option>
        </select>
        @error('user_role')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <div class="form-check form-switch">
          <input class="form-check-input" 
                 type="checkbox" 
                 id="is_active" 
                 name="is_active" 
                 value="1"
                 {{ old('is_active') ? 'checked' : 'checked' }}>
          <label class="form-check-label" for="is_active">Active Status</label>
        </div>
        <small class="form-text text-muted">If active, this user can log in to the admin panel</small>
      </div>

      <div class="mb-3">
        <button type="submit" class="btn btn-primary">Create Admin User</button>
        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection