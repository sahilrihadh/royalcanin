@extends('admin.layouts.master')

@section('title', 'Polls')
@section('page-title', 'Polls Management')

@section('breadcrumb')
<li class="breadcrumb-item active">Polls</li>
@endsection

@section('content')

<div class="flex items-center justify-end mb-4">
  <a href="{{ route('admin.polls.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-4 py-2 rounded-lg">
    <i class="fas fa-plus"></i> Create Poll
  </a>
</div>

@if(session('success'))
<div class="mb-4 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium px-4 py-3 rounded-lg">
  <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 flex items-center gap-2 bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium px-4 py-3 rounded-lg">
  <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<div class="bg-white border border-[#ebebee] rounded-xl overflow-hidden">
  <div class="p-4 border-b border-[#ebebee]">
    <h2 class="font-semibold text-[#09090b]">All Polls</h2>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
      <thead>
        <tr class="bg-gray-50">
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">ID</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Question</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Options</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Votes</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Active</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Correct Option</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Expires</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Created</th>
          <th class="px-4 py-3 w-32"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($polls as $poll)
        <tr class="border-b border-[#ebebee] hover:bg-gray-50">
          <td class="px-4 py-3 text-[#09090b]">{{ $poll->id }}</td>
          <td class="px-4 py-3 text-[#09090b] font-medium">{{ Str::limit($poll->question, 60) }}</td>
          <td class="px-4 py-3 text-[#09090b]">{{ $poll->options->count() }} options</td>
          <td class="px-4 py-3 text-[#09090b]">{{ $poll->votes->count() }}</td>
          <td class="px-4 py-3">
            <label class="toggle-switch">
              <input type="checkbox" class="poll-toggle-status" data-id="{{ $poll->id }}" {{ $poll->is_active ? 'checked' : '' }}>
              <span class="toggle-slider"></span>
            </label>
          </td>
          <td class="px-4 py-3">
            @php
              $correctOption = $poll->options->where('is_correct', true)->first();
            @endphp
            @if($correctOption)
              @include('admin.partials.status-pill', ['label' => Str::limit($correctOption->option_text, 30), 'color' => 'info'])
            @else
              @include('admin.partials.status-pill', ['label' => 'Not set', 'color' => 'warning'])
            @endif
          </td>
          <td class="px-4 py-3 text-[#09090b]">{{ $poll->expires_at ? $poll->expires_at->format('d M Y') : 'Never' }}</td>
          <td class="px-4 py-3 text-[#09090b]">{{ $poll->created_at->format('d M Y') }}</td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-1.5">
              <a href="{{ route('admin.polls.edit', $poll->id) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-[#ebebee] text-[#09090b] hover:bg-gray-50">
                <i class="fas fa-edit"></i>
              </a>
              <a href="{{ route('admin.polls.show', $poll->id) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-[#ebebee] text-[#09090b] hover:bg-gray-50">
                <i class="fas fa-chart-bar"></i>
              </a>
              <button class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 delete-poll" data-id="{{ $poll->id }}" data-name="{{ $poll->question }}">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" class="text-center py-8 text-gray-500">No polls found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="border-t border-[#ebebee]">
    @include('admin.partials.pagination', ['paginator' => $polls, 'perPage' => 10])
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function() {
    // Toggle poll status
    $('.poll-toggle-status').on('change', function() {
      var pollId = $(this).data('id');
      var $btn = $(this);

      $btn.prop('disabled', true);

      $.ajax({
        url: '/admin/polls/' + pollId + '/toggle-status',
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}'
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
              _token: '{{ csrf_token() }}'
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
@endpush
