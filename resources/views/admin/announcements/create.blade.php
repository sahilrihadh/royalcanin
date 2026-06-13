@extends('admin.layouts.master')

@section('title', 'Create Announcement')
@section('page-title', 'Create New Announcement')

@section('content')
<div class="card">
  <div class="card-header">
    <h5>Create Announcement</h5>
    <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary btn-sm float-end">Back to List</a>
  </div>
  <div class="card-body">
    <form id="announcementForm" action="{{ route('admin.announcements.store') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" 
               class="form-control @error('title') is-invalid @enderror" 
               id="title" 
               name="title" 
               value="{{ old('title') }}" 
               required>
        @error('title')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
        <textarea class="form-control @error('description') is-invalid @enderror" 
                  id="description" 
                  name="description" 
                  rows="5" 
                  required>{{ old('description') }}</textarea>
        @error('description')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-control @error('status') is-invalid @enderror" 
                id="status" 
                name="status" 
                required>
          <option value="hide" {{ old('status') == 'hide' ? 'selected' : '' }}>Hide</option>
          <option value="show" {{ old('status') == 'show' ? 'selected' : '' }}>Show</option>
        </select>
        @error('status')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">If set to "Show", this announcement will be visible to users immediately.</small>
      </div>

      <div class="mb-3">
        <button type="submit" class="btn btn-primary">Create Announcement</button>
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection