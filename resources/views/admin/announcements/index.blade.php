@extends('admin.layouts.master')

@section('title', 'Announcements')
@section('page-title', 'Announcement Management')

@section('breadcrumb')
<li class="breadcrumb-item active">Announcements</li>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <h5>Announcements</h5>
    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm float-end">Create New Announcement</a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($announcements as $announcement)
          <tr>
            <td>{{ $announcement->id }}</td>
            <td>{{ $announcement->title }}</td>
            <td>{{ Str::limit($announcement->description, 100) }}</td>
            <td>
              <label class="toggle-switch align-middle">
                <input type="checkbox" class="announcement-toggle-status" data-id="{{ $announcement->id }}" data-current-status="{{ $announcement->status }}" {{ $announcement->status === 'show' ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
              </label>
              <span class="ms-2 status-label">{{ $announcement->status === 'show' ? 'Visible' : 'Hidden' }}</span>
            </td>
            <td>{{ $announcement->created_at ? $announcement->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
            <td>
              <a href="{{ route('admin.announcements.edit', $announcement->id) }}" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>

              <button type="button" class="btn btn-danger btn-sm delete-announcement"
                      data-id="{{ $announcement->id }}"
                      data-title="{{ $announcement->title }}">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center">No announcements found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    
    // Toggle Status - No Page Reload
    $('.announcement-toggle-status').on('change', function() {
        var $checkbox = $(this);
        var id = $checkbox.data('id');
        var currentStatus = $checkbox.data('current-status');
        var wasVisible = currentStatus === 'show';

        var actionDisplayText = wasVisible ? 'Hide' : 'Show';

        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to ${actionDisplayText.toLowerCase()} this announcement?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Yes, ${actionDisplayText} it!`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) {
                $checkbox.prop('checked', wasVisible);
                return;
            }

            $checkbox.prop('disabled', true);

            $.ajax({
                url: `/admin/announcements/${id}/toggle-status`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var $row = $checkbox.closest('tr');
                        var $label = $row.find('.status-label');

                        if (response.status === 'show') {
                            $label.text('Visible');
                            $checkbox.data('current-status', 'show').prop('checked', true);
                            toastr.success('Announcement is now visible to users!', 'Shown!');
                        } else {
                            $label.text('Hidden');
                            $checkbox.data('current-status', 'hide').prop('checked', false);
                            toastr.success('Announcement is now hidden from users!', 'Hidden!');
                        }

                        // Show real-time notification
                        toastr.info('Real-time notification sent to webcast page!', 'Broadcast Sent');
                    } else {
                        toastr.error('Something went wrong!', 'Error!');
                        $checkbox.prop('checked', wasVisible);
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to toggle status!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    toastr.error(errorMsg, 'Error!');
                    $checkbox.prop('checked', wasVisible);
                },
                complete: function() {
                    $checkbox.prop('disabled', false);
                }
            });
        });
    });
    
    // Delete Announcement - No Page Reload
    $('.delete-announcement').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var id = $btn.data('id');
        var title = $btn.data('title');
        
        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete announcement "${title}". This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                
                $.ajax({
                    url: `/admin/announcements/${id}`,
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove the row from table with fade effect
                            $btn.closest('tr').fadeOut(300, function() {
                                $(this).remove();
                                
                                // Check if table is empty
                                if ($('tbody tr').length === 0) {
                                    $('tbody').html('<tr><td colspan="6" class="text-center">No announcements found.</td></tr>');
                                }
                            });
                            toastr.success(response.message, 'Deleted!');
                        } else {
                            toastr.error('Something went wrong!', 'Error!');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to delete announcement!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastr.error(errorMsg, 'Error!');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            }
        });
    });
});
</script>
@endpush
@endsection