<?php $__env->startSection('title', 'Polls'); ?>
<?php $__env->startSection('page-title', 'Polls Management'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item active">Polls</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">All Polls</h5>
    <a href="<?php echo e(route('admin.polls.create')); ?>" class="btn btn-primary btn-sm float-end">
      <i class="fas fa-plus"></i> Create Poll
    </a>
  </div>
  <div class="card-body p-0">
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo e(session('success')); ?>

      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?php echo e(session('error')); ?>

      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Question</th>
            <th>Options</th>
            <th>Total Votes</th>
            <th>Status</th>
            <th>Correct Option</th>
            <th>Expires At</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $polls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e($poll->id); ?> </td>
            <td><?php echo e(Str::limit($poll->question, 60)); ?> </td>
            <td><?php echo e($poll->options->count()); ?> options</td>
            <td><?php echo e($poll->votes->count()); ?> </td>
            <td>
              <span class="badge <?php echo e($poll->is_active ? 'bg-success' : 'bg-secondary'); ?>">
                <?php echo e($poll->is_active ? 'Active' : 'Inactive'); ?>

              </span>
             </td>
            <td>
              <?php
                $correctOption = $poll->options->where('is_correct', true)->first();
              ?>
              <?php if($correctOption): ?>
                <span class="badge bg-info"><?php echo e(Str::limit($correctOption->option_text, 30)); ?></span>
              <?php else: ?>
                <span class="badge bg-warning">Not set</span>
              <?php endif; ?>
             </td>
            <td><?php echo e($poll->expires_at ? $poll->expires_at->format('d M Y') : 'Never'); ?> </td>
            <td><?php echo e($poll->created_at->format('d M Y')); ?> </td>
            <td>
              <button class="btn btn-sm btn-warning toggle-status" data-id="<?php echo e($poll->id); ?>">
                <i class="fas <?php echo e($poll->is_active ? 'fa-pause' : 'fa-play'); ?>"></i>
              </button>
              <a href="<?php echo e(route('admin.polls.edit', $poll->id)); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i>
              </a>
              <button class="btn btn-sm btn-danger delete-poll" data-id="<?php echo e($poll->id); ?>" data-name="<?php echo e($poll->question); ?>">
                <i class="fas fa-trash"></i>
              </button>
              <a href="<?php echo e(route('admin.polls.show', $poll->id)); ?>" class="btn btn-sm btn-info">
    <i class="fas fa-chart-bar"></i> Results
</a>
             </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="9" class="text-center">No polls found</td>
          </tr>
          <?php endif; ?>
        </tbody>
       </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
      <?php echo e($polls->links()); ?>

    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function() {
    // Toggle poll status
    $('.toggle-status').on('click', function() {
      var pollId = $(this).data('id');
      var $btn = $(this);

      $btn.html('<i class="fas fa-spinner fa-spin"></i>');
      $btn.prop('disabled', true);

      $.ajax({
        url: '/admin/polls/' + pollId + '/toggle-status',
        type: 'POST',
        data: {
          _token: '<?php echo e(csrf_token()); ?>'
        },
        success: function(response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
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
            }).then(() => {
              location.reload();
            });
          }
        },
        error: function(xhr) {
          var message = xhr.responseJSON?.message || 'Failed to update status';
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: message,
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            location.reload();
          });
        }
      });
    });

    // Delete poll
    $('.delete-poll').on('click', function() {
      var pollId = $(this).data('id');

      Swal.fire({
        title: 'Are you sure?',
        text: "You want to delete this poll?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '/admin/polls/' + pollId,
            type: 'DELETE',
            data: {
              _token: '<?php echo e(csrf_token()); ?>'
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
                text: 'Failed to delete poll',
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sahilrihadh/Development/royalcanin/webinar-solution/resources/views/admin/polls/index.blade.php ENDPATH**/ ?>