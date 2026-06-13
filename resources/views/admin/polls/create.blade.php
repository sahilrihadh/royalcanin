@extends('admin.layouts.master')

@section('title', 'Create Poll')
@section('page-title', 'Create New Poll')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.polls.index') }}">Polls</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="card" x-data="pollForm()">
  <div class="card-header">
    <h5 class="mb-0">Create Poll</h5>
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

    <form method="POST" action="{{ route('admin.polls.store') }}">
      @csrf

      <div class="mb-3">
        <label class="form-label">Poll Question</label>
        <textarea name="question" class="form-control @error('question') is-invalid @enderror" rows="3" required placeholder="Enter your poll question...">{{ old('question') }}</textarea>
        @error('question')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Options (Minimum 2) - Select the correct answer</label>
        <div id="options-container">
          <template x-for="(option, index) in options" :key="index">
            <div class="input-group mb-2 option-group">
              <div class="input-group-text">
                <input type="radio" name="correct_option" :value="index" class="form-check-input mt-0" :required="options.length > 0">
              </div>
              <input type="text" 
                     name="options[]" 
                     class="form-control" 
                     :placeholder="'Option ' + (index + 1)" 
                     x-model="options[index]"
                     required>
              <button type="button" class="btn btn-danger remove-option" x-show="options.length > 2" @click="removeOption(index)">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </template>
        </div>
        <button type="button" class="btn btn-sm btn-success mt-2" @click="addOption">
          <i class="fas fa-plus"></i> Add Option
        </button>
        <div class="form-text text-muted mt-2">
          <i class="fas fa-info-circle"></i> Select the radio button next to the correct answer
        </div>
        @error('options')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
        @error('correct_option')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
      </div>

      

      <button type="submit" class="btn btn-primary">Create Poll</button>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function pollForm() {
    return {
      options: @json(old('options', ['', ''])),
      
      addOption() {
        this.options.push('');
      },
      
      removeOption(index) {
        if (this.options.length > 2) {
          this.options.splice(index, 1);
        }
      }
    }
  }
</script>
@endpush
@endsection