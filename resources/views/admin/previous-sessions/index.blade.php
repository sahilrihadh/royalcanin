{{-- resources/views/admin/previous-sessions/index.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Previous Sessions')
@section('page-title', 'Previous Sessions & Certificates')

@section('breadcrumb')
<li class="breadcrumb-item active">Previous Sessions</li>
@endsection

@section('content')

<div class="flex items-center justify-end mb-4">
  <a href="{{ route('admin.previous-sessions.export', ['session_name' => $sessionName]) }}" class="inline-flex items-center gap-2 border border-[#ebebee] text-[#09090b] hover:bg-gray-50 font-semibold text-sm px-4 py-2 rounded-lg">
    <i class="fas fa-file-excel"></i> Export Excel
  </a>
</div>

<div class="bg-white border border-[#ebebee] rounded-xl overflow-hidden">
  <div class="p-4 border-b border-[#ebebee]">
    <h2 class="font-semibold text-[#09090b] mb-3">Session Participants</h2>

    <!-- Webinar filter chips -->
    <div class="flex flex-wrap gap-2">
      <a href="{{ route('admin.previous-sessions.index') }}"
         class="text-sm font-semibold px-3 py-1.5 rounded-lg {{ !$sessionName ? 'bg-blue-600 text-white' : 'border border-[#ebebee] text-[#09090b] hover:bg-gray-50' }}">
        All
      </a>
      @foreach($webinars as $webinar)
      <a href="{{ route('admin.previous-sessions.index', ['session_name' => $webinar]) }}"
         class="text-sm font-semibold px-3 py-1.5 rounded-lg {{ $sessionName === $webinar ? 'bg-blue-600 text-white' : 'border border-[#ebebee] text-[#09090b] hover:bg-gray-50' }}">
        {{ ucfirst($webinar) }}
      </a>
      @endforeach
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
      <thead>
        <tr class="bg-gray-50">
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">ID</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Name</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Email</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Webinar</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Watched On</th>
          <th class="px-4 py-3 text-[#09090b] opacity-60 uppercase text-xs font-semibold tracking-wide">Certificate</th>
          <th class="px-4 py-3 w-24"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($sessions as $session)
        <tr class="border-b border-[#ebebee] hover:bg-gray-50">
          <td class="px-4 py-3 text-[#09090b]">{{ $session->id }}</td>
          <td class="px-4 py-3 text-[#09090b] font-medium">{{ $session->name }}</td>
          <td class="px-4 py-3 text-[#09090b]">{{ $session->email_id }}</td>
          <td class="px-4 py-3 text-[#09090b]">{{ ucfirst($session->session_name) }}</td>
          <td class="px-4 py-3 text-[#09090b]">{{ $session->watched_on ? $session->watched_on->format('d M Y H:i') : 'N/A' }}</td>
          <td class="px-4 py-3">
            @if($session->certificate_status == 1)
              @include('admin.partials.status-pill', ['label' => 'Sent', 'color' => 'success'])
            @else
              @include('admin.partials.status-pill', ['label' => 'Pending', 'color' => 'warning'])
            @endif
          </td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-1.5">
              @if($session->certificate_status != 1)
              <button class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-[#ebebee] text-[#09090b] hover:bg-gray-50 resend-certificate" data-id="{{ $session->id }}">
                <i class="fas fa-envelope"></i>
              </button>
              @endif
              <button class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 delete-single" data-id="{{ $session->id }}">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center py-8 text-gray-500">No sessions found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="border-t border-[#ebebee]">
    @include('admin.partials.pagination', [
      'paginator' => $sessions->appends(['session_name' => $sessionName]),
      'perPage' => 20,
    ])
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Resend Certificate
    $('.resend-certificate').on('click', function() {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Resend Certificate?',
            text: "This will resend the certificate to the participant's email.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, resend it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/previous-sessions/' + id + '/resend-certificate',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sent!',
                            text: 'Certificate resent successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to resend certificate',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    });

    // Delete single record
    $(document).on('click', '.delete-single', function() {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this session record?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/previous-sessions/' + id,
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
                            text: 'Failed to delete record',
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
