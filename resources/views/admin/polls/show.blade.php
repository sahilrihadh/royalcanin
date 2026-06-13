{{-- resources/views/admin/polls/show.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Poll Results')
@section('page-title', 'Poll Results & User Votes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.polls.index') }}">Polls</a></li>
<li class="breadcrumb-item active">Results #{{ $poll->id }}</li>
@endsection

@section('content')
<div class="row">
  <!-- Poll Information -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Poll Information</h5>
        <a href="{{ route('admin.polls.index') }}" class="btn btn-secondary btn-sm float-end">
          <i class="fas fa-arrow-left"></i> Back
        </a>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-8">
            <h4>{{ $poll->question }}</h4>
            <p class="text-muted">
              Created: {{ $poll->created_at->format('d M Y h:i A') }} |
              Status: <span class="badge {{ $poll->is_active ? 'bg-success' : 'bg-secondary' }}">
                {{ $poll->is_active ? 'Active' : 'Inactive' }}
              </span> |
              Total Votes: <strong>{{ $totalVotes }}</strong>
            </p>
          </div>
          <div class="col-md-4 text-end">
            <button class="btn btn-sm btn-primary" onclick="window.print()">
              <i class="fas fa-print"></i> Print Report
            </button>
            <button class="btn btn-sm btn-success" id="exportCSV">
              <i class="fas fa-download"></i> Export CSV
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Results Chart Section -->
  <div class="col-md-6 mt-3">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0">Vote Distribution</h6>
      </div>
      <div class="card-body">
        <canvas id="voteChart" style="max-height: 300px;"></canvas>
      </div>
    </div>
  </div>

  <!-- Results Summary -->
  <div class="col-md-6 mt-3">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0">Results Summary</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Option</th>
                <th>Correct?</th>
                <th>Votes</th>
                <th>Percentage</th>
              </tr>
            </thead>
            <tbody>
              @foreach($poll->options as $option)
              <tr class="{{ $option->is_correct ? 'table-success' : '' }}">
                <td>
                  {{ $option->option_text }}
                  @if($option->is_correct)
                    <span class="badge bg-success ms-2"><i class="fas fa-check"></i> Correct</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($option->is_correct)
                    <i class="fas fa-check-circle text-success"></i>
                  @else
                    <i class="fas fa-times-circle text-danger"></i>
                  @endif
                </td>
                <td>{{ $option->vote_count }}</td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="progress flex-grow-1" style="height: 8px;">
                      <div class="progress-bar bg-{{ $option->is_correct ? 'success' : 'primary' }}" 
                           style="width: {{ $totalVotes > 0 ? ($option->vote_count / $totalVotes) * 100 : 0 }}%">
                      </div>
                    </div>
                    <span class="ms-2">{{ $totalVotes > 0 ? round(($option->vote_count / $totalVotes) * 100, 1) : 0 }}%</span>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <div class="alert alert-info mt-3">
          <i class="fas fa-info-circle"></i> 
          <strong>Correct Answer:</strong> 
          @php $correct = $poll->options->where('is_correct', true)->first(); @endphp
          {{ $correct ? $correct->option_text : 'Not set' }}
        </div>
      </div>
    </div>
  </div>

  <!-- User Votes Table -->
  <div class="col-md-12 mt-3">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0">User Votes ({{ $userVotes->total() }} users)</h6>
        <div class="float-end">
          <input type="text" id="searchVotes" class="form-control form-control-sm" placeholder="Search user..." style="width: 200px;">
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover" id="votesTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>User Name</th>
                <th>Email</th>
                <th>Selected Option</th>
                <th>Correct/Incorrect</th>
                <th>Voted At</th>
              </tr>
            </thead>
            <tbody>
              @forelse($userVotes as $vote)
              <tr>
                <td>{{ $vote->id }}</td>
                <td>
                  <strong>{{ $vote->user->full_name ?? 'N/A' }}</strong>
                  <br>
                  <small class="text-muted">{{ $vote->user->clinic_name ?? '' }}</small>
                </td>
                <td>{{ $vote->user->email_id ?? 'N/A' }}</td>
                <td>{{ $vote->option->option_text ?? 'N/A' }}</td>
                <td>
                  @if($vote->is_correct)
                    <span class="badge bg-success"><i class="fas fa-check"></i> Correct</span>
                  @else
                    <span class="badge bg-danger"><i class="fas fa-times"></i> Incorrect</span>
                  @endif
                </td>
                <td>{{ $vote->created_at->format('d M Y h:i A') }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center">No votes found for this poll</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
          {{ $userVotes->links() }}
        </div>
        
        <!-- Statistics Footer -->
        <div class="row mt-4">
          <div class="col-md-4">
            <div class="card bg-light">
              <div class="card-body text-center">
                <h6>Total Votes</h6>
                <h4>{{ $totalVotes }}</h4>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-light">
              <div class="card-body text-center">
                <h6>Correct Votes</h6>
                <h4 class="text-success">{{ $correctVotes }}</h4>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-light">
              <div class="card-body text-center">
                <h6>Incorrect Votes</h6>
                <h4 class="text-danger">{{ $incorrectVotes }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
  // Chart.js for vote distribution
  var ctx = document.getElementById('voteChart').getContext('2d');
  var options = @json($poll->options->map(function($option) {
    return $option->option_text;
  }));
  var votes = @json($poll->options->map(function($option) use ($totalVotes) {
    return $totalVotes > 0 ? round(($option->vote_count / $totalVotes) * 100, 1) : 0;
  }));
  var colors = ['#6993ff', '#008080', '#e3242b', '#ffbd59', '#050357', '#28a745', '#dc3545', '#17a2b8'];
  
  var chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: options,
      datasets: [{
        label: 'Vote Percentage (%)',
        data: votes,
        backgroundColor: colors.slice(0, options.length),
        borderColor: '#333',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      scales: {
        y: {
          beginAtZero: true,
          max: 100,
          title: {
            display: true,
            text: 'Percentage (%)'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Poll Options'
          }
        }
      },
      plugins: {
        legend: {
          position: 'top',
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return context.dataset.label + ': ' + context.raw + '%';
            }
          }
        }
      }
    }
  });
  
  // Search functionality
  $('#searchVotes').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('#votesTable tbody tr').filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });
  
  // Export CSV
  $('#exportCSV').on('click', function() {
    var csvData = [];
    // Headers
    csvData.push(['User Name', 'Email', 'Clinic', 'Selected Option', 'Result', 'Voted At']);
    
    // Data
    @foreach($userVotes as $vote)
    csvData.push([
      '{{ addslashes($vote->user->full_name ?? "N/A") }}',
      '{{ $vote->user->email_id ?? "N/A" }}',
      '{{ addslashes($vote->user->clinic_name ?? "N/A") }}',
      '{{ addslashes($vote->option->option_text ?? "N/A") }}',
      '{{ $vote->is_correct ? "Correct" : "Incorrect" }}',
      '{{ $vote->created_at->format("d M Y h:i A") }}'
    ]);
    @endforeach
    
    var csvContent = csvData.map(row => row.join(',')).join('\n');
    var blob = new Blob([csvContent], { type: 'text/csv' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'poll_results_{{ $poll->id }}.csv';
    link.click();
    URL.revokeObjectURL(link.href);
  });
});
</script>
@endpush