@extends('admin.layouts.master')

@section('title', 'Questions')
@section('page-title', 'Questions Management')

@section('breadcrumb')
<li class="breadcrumb-item active">Questions</li>
@endsection

@section('content')

<div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
  <div class="flex flex-wrap gap-3">
    @include('admin.partials.stat-card', ['icon' => 'fa-list', 'value' => $stats['total'], 'label' => 'Total Questions'])
    @include('admin.partials.stat-card', ['icon' => 'fa-check', 'value' => $stats['answered'], 'label' => 'Answered'])
    @include('admin.partials.stat-card', ['icon' => 'fa-clock', 'value' => $stats['pending'], 'label' => 'Pending'])
  </div>
</div>

<div class="flex items-center justify-end mb-4">
  <a href="{{ route('admin.questions.export') }}" class="inline-flex items-center gap-2 border border-[#ebebee] text-[#09090b] hover:bg-gray-50 font-semibold text-sm px-4 py-2 rounded-lg">
    <i class="fas fa-file-excel"></i> Export to Excel
  </a>
</div>

<div class="bg-white border border-[#ebebee] rounded-xl overflow-hidden">
  <div class="p-4 border-b border-[#ebebee]">
    <h2 class="font-semibold text-[#09090b]">All Questions &amp; Answers</h2>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left" id="questionsTable">
      <thead>
        <tr class="bg-gray-50">
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">ID</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">User</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Email</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide w-[25%]">Question</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide w-[25%]">Answer / Reply</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Status</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Submitted</th>
          <th class="px-4 py-3 w-24"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($questions as $question)
        <tr id="question-row-{{ $question->id }}" class="border-b border-[#ebebee] hover:bg-gray-50">
          <td class="px-4 py-3 text-[#09090b]">{{ $question->id }}</td>
          <td class="px-4 py-3 text-[#09090b] font-medium">{{ $question->user->full_name ?? $question->user->name ?? 'N/A' }}</td>
          <td class="px-4 py-3 text-[#09090b]">{{ $question->user->email_id ?? $question->user->email ?? 'N/A' }}</td>
          <td class="px-4 py-3 text-[#09090b] break-words">{{ $question->question_text ?? $question->question_input }}</td>
          <td class="px-4 py-3 text-[#09090b]" id="answer-cell-{{ $question->id }}">
            @if($question->is_answered)
              <div class="bg-gray-50 border border-[#ebebee] rounded-lg p-2 text-sm">{{ Str::limit($question->answer_text, 100) }}</div>
            @else
              @include('admin.partials.status-pill', ['label' => 'Awaiting answer', 'color' => 'warning'])
            @endif
          </td>
          <td class="px-4 py-3" id="status-cell-{{ $question->id }}">
            @if($question->is_answered)
              @include('admin.partials.status-pill', ['label' => 'Answered', 'color' => 'success'])
            @else
              @include('admin.partials.status-pill', ['label' => 'Pending', 'color' => 'warning'])
            @endif
          </td>
          <td class="px-4 py-3 text-[#09090b] whitespace-nowrap">{{ $question->created_at->format('d M Y h:i A') }}</td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-1.5">
              <button class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-[#ebebee] text-[#09090b] hover:bg-gray-50 answer-question"
                      data-id="{{ $question->id }}"
                      data-question="{{ $question->question_text ?? $question->question_input }}"
                      data-user="{{ $question->user->full_name ?? $question->user->name ?? 'N/A' }}"
                      data-current-answer="{{ $question->answer_text ?? '' }}">
                <i class="fas fa-{{ $question->is_answered ? 'edit' : 'reply' }}"></i>
              </button>
              <button class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 delete-question"
                      data-id="{{ $question->id }}"
                      data-question="{{ Str::limit($question->question_text ?? $question->question_input, 50) }}">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center py-8 text-gray-500">No questions found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="border-t border-[#ebebee]">
    @include('admin.partials.pagination', ['paginator' => $questions, 'perPage' => 20])
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
        const $statusCell = $('#status-cell-' + questionId);

        // Update answer cell
        $answerCell.html(`
            <div class="bg-gray-50 border border-[#ebebee] rounded-lg p-2 text-sm">${escapeHtml(answerText.substring(0, 100))}${answerText.length > 100 ? '...' : ''}</div>
        `);

        // Update status pill
        $statusCell.html(`
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border bg-emerald-50 border-emerald-200 text-emerald-600">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Answered
            </span>
        `);

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
