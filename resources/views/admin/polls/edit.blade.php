@extends('admin.layouts.master')

@section('title', 'Edit Poll')
@section('page-title', 'Edit Poll')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.polls.index') }}">Polls</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Edit Poll</h5>
    <a href="{{ route('admin.polls.index') }}" class="btn btn-secondary btn-sm float-end">
      <i class="fas fa-arrow-left"></i> Back
    </a>
  </div>
  <div class="card-body">
    @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.polls.update', $poll->id) }}">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label">Poll Question</label>
        <textarea name="question" class="form-control @error('question') is-invalid @enderror" rows="3" required>{{ old('question', $poll->question) }}</textarea>
        @error('question')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Options (Minimum 2) - Select the correct answer</label>
        <div id="options-container">
          @foreach($poll->options as $index => $option)
          <div class="input-group mb-2 option-group">
            <div class="input-group-text">
              <input type="radio" 
                     name="correct_option" 
                     value="{{ $index }}" 
                     class="form-check-input mt-0" 
                     {{ $option->is_correct ? 'checked' : '' }}
                     required>
            </div>
            <input type="hidden" name="option_ids[]" value="{{ $option->id }}">
            <input type="text" 
                   name="options[]" 
                   class="form-control" 
                   placeholder="Option {{ $index + 1 }}" 
                   value="{{ old('options.' . $index, $option->option_text) }}"
                   required>
            <button type="button" class="btn btn-danger remove-option" {{ $poll->options->count() <= 2 ? 'style="display: none;"' : '' }}>
              <i class="fas fa-trash"></i>
            </button>
          </div>
          @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-success mt-2" id="add-option">
          <i class="fas fa-plus"></i> Add Option
        </button>
        @error('options')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
        @error('correct_option')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
      </div>

      

      <button type="submit" class="btn btn-primary">Update Poll</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    let optionCount = {{ $poll->options->count() }};
    
    $('#add-option').on('click', function() {
      optionCount++;
      var newOption = `
        <div class="input-group mb-2 option-group">
          <div class="input-group-text">
            <input type="radio" name="correct_option" value="${optionCount - 1}" class="form-check-input mt-0">
          </div>
          <input type="hidden" name="option_ids[]" value="">
          <input type="text" name="options[]" class="form-control" placeholder="Option ${optionCount}" required>
          <button type="button" class="btn btn-danger remove-option">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      `;
      $('#options-container').append(newOption);
      $('.remove-option').show();
    });

    $(document).on('click', '.remove-option', function() {
      if ($('.option-group').length > 2) {
        $(this).closest('.option-group').remove();
        optionCount--;
        
        // Re-index radio button values
        $('.option-group').each(function(index) {
          $(this).find('input[name="correct_option"]').val(index);
        });
      }

      if ($('.option-group').length <= 2) {
        $('.remove-option').hide();
      }
    });
  });
</script>
@endpush