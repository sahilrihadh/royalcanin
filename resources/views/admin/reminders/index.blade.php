@extends('admin.layouts.master')

@section('title', 'Reminder Email')
@section('page-title', 'Webinar Reminder Email')

@section('breadcrumb')
<li class="breadcrumb-item active">Reminder Email</li>
@endsection

@section('content')

<div class="flex flex-wrap gap-3 mb-6">
  @include('admin.partials.stat-card', ['icon' => 'fa-users', 'value' => $totalUsers, 'label' => 'Registered Users'])
</div>

<div class="bg-white border border-[#ebebee] rounded-xl overflow-hidden">
  <div class="p-4 border-b border-[#ebebee]">
    <h2 class="font-semibold text-[#09090b]">Send Webinar Reminder</h2>
    <p class="text-sm text-gray-500 mt-1">Emails every registered user with the branded GI Horizons reminder, highlighting the session you pick below.</p>
  </div>

  <div class="p-4 space-y-4" id="reminderForm">
    <div>
      <label class="block text-xs font-medium text-[#09090b] mb-1">Session to remind about</label>
      <select id="sessionSelect" class="border border-[#ebebee] rounded-lg text-sm px-3 py-2 min-w-[320px] text-[#09090b] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
        @foreach($sessions as $index => $session)
          <option value="{{ $index }}" {{ $nextSession && $nextSession['date'] === $session['date'] ? 'selected' : '' }}>
            {{ $session['date'] }} &mdash; {{ $session['topic'] }}
          </option>
        @endforeach
      </select>
    </div>

    <button id="startSendBtn" type="button" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-4 py-2 rounded-lg">
      <i class="fas fa-paper-plane"></i> Send Reminder Emails
    </button>

    <div id="progressArea" class="hidden pt-2">
      <div class="flex items-center justify-between text-sm text-[#09090b] mb-1">
        <span id="progressLabel">Sending&hellip;</span>
        <span><span id="sentCount">0</span> sent &middot; <span id="failedCount">0</span> failed &middot; <span id="processedCount">0</span>/<span id="totalCount">{{ $totalUsers }}</span></span>
      </div>
      <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
        <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all" style="width: 0%"></div>
      </div>

      <div id="logList" class="mt-4 max-h-64 overflow-y-auto border border-[#ebebee] rounded-lg divide-y divide-[#ebebee] text-sm"></div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  const batchSizeUrl = '{{ route("admin.reminders.send-batch") }}';
  const csrfToken = '{{ csrf_token() }}';
  const totalUsers = {{ $totalUsers }};

  let sentTotal = 0;
  let failedTotal = 0;

  function appendLog(name, email, ok) {
    const $row = $('<div class="px-3 py-2 flex items-center gap-2"></div>');
    const icon = ok ? '<i class="fas fa-check-circle text-emerald-500"></i>' : '<i class="fas fa-times-circle text-rose-500"></i>';
    $row.html(icon + ' <span class="text-[#09090b]">' + $('<div>').text(name).html() + '</span> <span class="text-gray-400">&lt;' + $('<div>').text(email).html() + '&gt;</span>');
    $('#logList').append($row);
    $('#logList').scrollTop($('#logList')[0].scrollHeight);
  }

  function sendNextBatch(offset, sessionIndex) {
    $.post(batchSizeUrl, {
      _token: csrfToken,
      offset: offset,
      session_index: sessionIndex
    }).done(function(res) {
      sentTotal += res.sent.length;
      failedTotal += res.failed.length;

      res.sent.forEach(u => appendLog(u.name, u.email, true));
      res.failed.forEach(u => appendLog(u.name, u.email, false));

      $('#sentCount').text(sentTotal);
      $('#failedCount').text(failedTotal);
      $('#processedCount').text(res.processed);
      $('#progressBar').css('width', Math.round((res.processed / res.total) * 100) + '%');

      if (!res.done) {
        sendNextBatch(res.next_offset, sessionIndex);
      } else {
        $('#progressLabel').text('Done');
        $('#startSendBtn').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Send Reminder Emails');
        toastr.success('Reminder emails finished sending.', 'Done');
      }
    }).fail(function() {
      $('#progressLabel').text('Stopped — a batch failed. Check the logs and try again.');
      $('#startSendBtn').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Send Reminder Emails');
      toastr.error('Sending stopped due to an error.', 'Error');
    });
  }

  $('#startSendBtn').on('click', function() {
    const sessionIndex = $('#sessionSelect').val();
    const sessionLabel = $('#sessionSelect option:selected').text();

    Swal.fire({
      title: 'Send reminder to all registered users?',
      html: 'This will email <strong>' + totalUsers + '</strong> registered users about:<br><em>' + sessionLabel + '</em>',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#2563eb',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Yes, send it!'
    }).then((result) => {
      if (!result.isConfirmed) {
        return;
      }

      sentTotal = 0;
      failedTotal = 0;
      $('#logList').empty();
      $('#sentCount').text(0);
      $('#failedCount').text(0);
      $('#processedCount').text(0);
      $('#progressBar').css('width', '0%');
      $('#progressLabel').text('Sending…');
      $('#progressArea').removeClass('hidden');
      $('#startSendBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending…');

      sendNextBatch(0, sessionIndex);
    });
  });
});
</script>
@endpush
