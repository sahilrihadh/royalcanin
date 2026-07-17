@extends('admin.layouts.master')

@section('title', 'Questions')
@section('page-title', 'Questions Management')

@section('breadcrumb')
<li class="breadcrumb-item active">Questions</li>
@endsection

@section('content')

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stats-card">
            <div class="card-body card-body d-flex flex-column justify-content-center align-items-center text-center">
                <h5 class="card-title">Total Questions</h5>
                <h3 class="mb-0">{{ $stats['total'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stats-card">
            <div class="card-body card-body d-flex flex-column justify-content-center align-items-center text-center">
                <h5 class="card-title">Answered</h5>
                <h3 class="mb-0">{{ $stats['answered'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stats-card">
            <div class="card-body card-body d-flex flex-column justify-content-center align-items-center text-center">
                <h5 class="card-title">Pending</h5>
                <h3 class="mb-0">{{ $stats['pending'] }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Questions & Answers</h5>
        <a href="{{ route('admin.questions.export') }}" class="btn btn-success btn-sm">
            <i class="fas fa-file-excel"></i> Export to Excel
        </a>
    </div>
    <div class="card-body p-0">
        <!-- Questions Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="questionsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th width="30%">Question</th>
                        <th width="30%">Answer / Reply</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $question)
                    <tr id="question-row-{{ $question->id }}">
                        <td>{{ $question->id }}</td>
                        <td>
                            <strong>{{ $question->user->full_name ?? $question->user->name ?? 'N/A' }}</strong>
                        </td>
                        <td>{{ $question->user->email_id ?? $question->user->email ?? 'N/A' }}</td>
                        <td>
                            <div style="word-wrap: break-word;">
                                {{ $question->question_text ?? $question->question_input }}
                            </div>
                        </td>
                        <td id="answer-cell-{{ $question->id }}">
                            @if($question->is_answered)
                                <div class="answer-box p-2 bg-light rounded">
                                    <p class="mb-0">{{ Str::limit($question->answer_text, 100) }}</p>
                                </div>
                            @else
                                <span class="badge bg-warning">Awaiting answer</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge status-badge-{{ $question->id }} {{ $question->is_answered ? 'bg-success' : 'bg-warning' }}">
                                {{ $question->is_answered ? 'Answered' : 'Pending' }}
                            </span>
                        </td>
                        <td>{{ $question->created_at->format('d M Y h:i A') }}</td>
                        <td>
                            <div class="d-flex gap-1" role="group">
                                <button class="btn btn-outline-primary answer-question" 
                                        data-id="{{ $question->id }}"
                                        data-question="{{ $question->question_text ?? $question->question_input }}"
                                        data-user="{{ $question->user->full_name ?? $question->user->name ?? 'N/A' }}"
                                        data-current-answer="{{ $question->answer_text ?? '' }}">
                                    <i class="fas fa-{{ $question->is_answered ? 'edit' : 'reply' }}"></i>
                                </button>
                                <button class="btn btn-outline-danger delete-question" 
                                        data-id="{{ $question->id }}"
                                        data-question="{{ Str::limit($question->question_text ?? $question->question_input, 50) }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No questions found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $questions->links() }}
        </div>
    </div>
</div>

<!-- Answer Modal -->
<div class="modal fade" id="answerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="answerModalTitle">Reply to Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">User:</label>
                    <p class="fw-bold" id="modalUserName"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Question:</label>
                    <div class="p-3 bg-light rounded" id="modalQuestionText"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label" id="answerLabel">Your Answer:</label>
                    <textarea id="answerText" class="form-control" rows="5" placeholder="Type your answer here..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitAnswer">
                    <i class="fas fa-paper-plane"></i> <span id="submitBtnText">Submit Answer</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentQuestionId = null;
    let isEditing = false;
    
    // Answer button click (works for both Reply and Edit)
    $('.answer-question').on('click', function() {
        currentQuestionId = $(this).data('id');
        const userName = $(this).data('user');
        const questionText = $(this).data('question');
        const currentAnswer = $(this).data('current-answer');
        const hasAnswer = currentAnswer && currentAnswer.trim() !== '';
        
        $('#modalUserName').text(userName);
        $('#modalQuestionText').text(questionText);
        
        if (hasAnswer) {
            $('#answerModalTitle').text('Edit Answer');
            $('#answerLabel').text('Edit Your Answer:');
            $('#answerText').val(currentAnswer);
            $('#submitBtnText').text('Update Answer');
            isEditing = true;
        } else {
            $('#answerModalTitle').text('Reply to Question');
            $('#answerLabel').text('Your Answer:');
            $('#answerText').val('');
            $('#submitBtnText').text('Submit Answer');
            isEditing = false;
        }
        
        $('#answerModal').modal('show');
    });
    
    // Submit/Update Answer
    $('#submitAnswer').on('click', function() {
        var answerText = $('#answerText').val().trim();
        
        if (!answerText) {
            Swal.fire({
                icon: 'warning',
                title: 'Empty Answer',
                text: 'Please enter an answer before submitting.',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }
        
        const actionText = isEditing ? 'update' : 'submit';
        
        Swal.fire({
            title: isEditing ? 'Update Answer?' : 'Submit Answer?',
            text: isEditing ? "This will update the existing answer." : "This answer will be visible to the user.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: isEditing ? 'Yes, update it!' : 'Yes, submit it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const $btn = $('#submitAnswer');
                const originalText = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                
                $.ajax({
                    url: '/admin/questions/' + currentQuestionId + '/answer',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        answer_text: answerText
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update the table row without reload
                            updateQuestionRow(currentQuestionId, answerText);
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                $('#answerModal').modal('hide');
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to ' + actionText + ' answer';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            }
        });
    });
    
    // Update question row dynamically
    function updateQuestionRow(questionId, answerText) {
        const $row = $('#question-row-' + questionId);
        const $answerCell = $('#answer-cell-' + questionId);
        const $statusBadge = $('.status-badge-' + questionId);
        
        // Update answer cell
        $answerCell.html(`
            <div class="answer-box p-2 bg-light rounded">
                <p class="mb-0">${escapeHtml(answerText.substring(0, 100))}${answerText.length > 100 ? '...' : ''}</p>
            </div>
        `);
        
        // Update status badge
        $statusBadge.removeClass('bg-warning').addClass('bg-success');
        $statusBadge.text('Answered');
        
        // Update the action button
        const $actionBtn = $row.find('.answer-question');
        $actionBtn.html('<i class="fas fa-edit"></i>');
        $actionBtn.data('current-answer', answerText);
        
        // Show success toast
        toastr.success('Answer updated successfully!', 'Success');
    }
    
    // Escape HTML helper
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Delete single record
    $(document).on('click', '.delete-question', function() {
        var id = $(this).data('id');
        var questionText = $(this).data('question');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "Delete question: \"" + questionText + "\"?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/questions/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#question-row-' + id).fadeOut(300, function() {
                                $(this).remove();
                            });
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to delete question',
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