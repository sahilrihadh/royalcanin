@extends('layouts.master')

@section('title', 'Webcast | Royal Canin')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/simplebar@latest/dist/simplebar.css">
<link href="{{ asset('assets/css/main.min.css') }}" rel="stylesheet">
<style>
    /* Additional styles for better UX */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #dc2626;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 8px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .btn-loading {
        opacity: 0.7;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <!-- Video Wrapper -->
        <div class="col-lg-8 col-md-8 col-12">
            <div class="ratio ratio-16x9">
                <iframe src="{{ asset('player.php') }}" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Poll Sidebar Button -->
<div class="sidebar-btn sidebar-btn-left" id="pollSidebarCollapse">
    <img src="{{ asset('assets/img/poll.png') }}" class="img-fluid" alt="Poll">
    <span class="blob"></span>
    <div>ANSWER THE POLL</div>
</div>

<!-- Question Sidebar Button -->
<div class="sidebar-btn sidebar-btn-right" id="questionSidebarCollapse">
    <span class="blob"></span>
    <img src="{{ asset('assets/img/ask-questio.png') }}" class="img-fluid" alt="Question">
    <div>ASK A QUESTION</div>
</div>

<!-- Reactions -->
@include('partials.reactions')

<!-- Poll Sidebar -->
@include('partials.poll-sidebar')

<!-- Question Sidebar -->
@include('partials.question-sidebar')

@include('partials.announcement-modal')

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://unpkg.com/simplebar@latest/dist/simplebar.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ==================== CONFIGURATION ====================
    const userId = '{{ Auth::id() }}';
    const csrfToken = '{{ csrf_token() }}';

    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // ==================== PUSHER SETUP ====================
    const pusher = new Pusher('bef55ec683a7039d70a8', {
        cluster: 'ap2',
        authEndpoint: '/broadcasting/auth',
        auth: { headers: { 'X-CSRF-TOKEN': csrfToken } }
    });

    const pollChannel = pusher.subscribe('poll-channel');
    const questionChannel = pusher.subscribe('question-channel');
    const announcementChannel = pusher.subscribe('announcements');

    // ==================== ANNOUNCEMENTS ====================
    announcementChannel.bind('show-announcement', function(data) {
        document.getElementById('announcementModalBody').innerHTML = `
            <div class="text-center mb-3">
                <i class="fas fa-bullhorn" style="font-size: 48px; color: #dc2626;"></i>
            </div>
            <h4 class="text-danger text-center mb-3">${escapeHtml(data.title)}</h4>
            <p class="text-center mb-0">${escapeHtml(data.description)}</p>
            <hr>
            <small class="text-muted d-block text-center">${new Date().toLocaleString()}</small>
        `;
        new bootstrap.Modal(document.getElementById('announcementModal')).show();
    });

    announcementChannel.bind('hide-announcement', function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('announcementModal'));
        if (modal) modal.hide();
    });

    // ==================== POLL FUNCTIONS ====================
    let isPollSubmitting = false;

    function loadPoll() {
        axios.post('{{ route("check.poll") }}', { request: 1 })
            .then(res => {
                if (res.data && res.data.has_poll && res.data.poll.html) {
                    $('#poll').html(res.data.poll.html);
                    $('#pollSidebar').addClass('active');
                } else {
                    closePoll();
                }
            })
            .catch(err => console.error('Poll error:', err));
    }

    function closePoll() {
        $("#pollSidebar").removeClass("active");
        $('#poll').html('<div class="alert alert-info">No active poll available.</div>');
    }

    $(document).on('click', '#but_vote', async function(e) {
        e.preventDefault();
        if (isPollSubmitting) return;
        
        const selectedOption = $("#poll input[name='poll']:checked").val();
        if (!selectedOption) {
            alert('Please select an option.');
            return;
        }

        isPollSubmitting = true;
        const $btn = $(this);
        const originalText = $btn.text();
        $btn.prop('disabled', true).html('<span class="loading-spinner"></span> Submitting...');

        try {
            const res = await axios.post('{{ route("submit.poll.vote") }}', {
                request: 2,
                poll: selectedOption,
                poll_id: $("#poll input[name='poll_id']").val()
            });

            if (res.data.success || res.data == 1) {
                alert('Vote submitted successfully!');
                loadPoll(); // Reload to show results
            } else {
                alert(res.data.message || 'Failed to submit vote');
                $btn.prop('disabled', false).html(originalText);
            }
        } catch (error) {
            alert('Error submitting vote');
            $btn.prop('disabled', false).html(originalText);
        } finally {
            isPollSubmitting = false;
        }
    });

    // Poll real-time updates
    pollChannel.bind('poll-status-changed', function() {
        loadPoll();
    });

    // ==================== QUESTIONS WITH REAL-TIME ANSWERS ====================

// Listen for question answered events
questionChannel.bind('question-answered', function(data) {
    console.log('📢 Question answered:', data);
    
    // Show notification
    showToastNotification('Question Answered', `${data.user_name}'s question has been answered!`);
    
    // Reload questions to show the new answer
    loadQuestions();
});

// Load questions function
function loadQuestions() {
    axios.post('{{ route("get-questions") }}', { request: 3 })
        .then(res => {
            if (res.data) {
                $('#messages').html(res.data);
                // Scroll to bottom to show latest
                const messagesDiv = document.getElementById('messages');
                if (messagesDiv) {
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }
            }
        })
        .catch(err => console.error('Questions error:', err));
}

// Show toast notification
function showToastNotification(title, message) {
    if (!$('#toastContainer').length) {
        $('body').append('<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>');
    }
    
    const toastId = 'toast_' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast show" role="alert" style="min-width: 300px; margin-bottom: 10px;">
            <div class="toast-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">${title}</strong>
                <small>Just now</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${escapeHtml(message)}
            </div>
        </div>
    `;
    
    $('#toastContainer').append(toastHtml);
    
    setTimeout(() => {
        $(`#${toastId}`).fadeOut(300, function() {
            $(this).remove();
        });
    }, 5000);
}

    // ==================== REACTIONS ====================
    let submittedReactions = JSON.parse(sessionStorage.getItem('submittedReactions') || '{}');
    let reactionTimer = null;

    $('.reaction-btn').on('click', function() {
        const type = $(this).data('reaction');
        
        if (submittedReactions[type]) {
            createReactionAnimation(type);
            if (type === 'applause') playApplause();
            return;
        }
        
        if (reactionTimer) {
            createReactionAnimation(type);
            if (type === 'applause') playApplause();
            return;
        }
        
        reactionTimer = setTimeout(() => reactionTimer = null, 3000);
        createReactionAnimation(type);
        if (type === 'applause') playApplause();
        
        submittedReactions[type] = true;
        sessionStorage.setItem('submittedReactions', JSON.stringify(submittedReactions));
        
        axios.post('{{ route("store-reaction") }}', { reaction: type })
            .catch(err => console.error('Reaction error:', err));
    });

    function createReactionAnimation(type) {
        const container = document.getElementById('heartContainer');
        if (!container) return;
        
        for (let i = 0; i < 30; i++) {
            const el = document.createElement('div');
            el.className = type === 'love' ? 'heart' : (type === 'like' ? 'like' : 'clap');
            el.style.cssText = `
                position: fixed;
                left: ${Math.random() * 100}vw;
                bottom: -20px;
                width: ${Math.random() * 30 + 10}px;
                height: ${Math.random() * 30 + 10}px;
                animation: floatUp ${Math.random() * 4 + 4}s ease-out forwards;
                z-index: 9999;
                pointer-events: none;
            `;
            container.appendChild(el);
            el.addEventListener('animationend', () => el.remove());
        }
    }
    
    function playApplause() {
        new Audio('{{ asset("assets/audio/audience-clapping.mp3") }}').play().catch(e => console.log(e));
    }

    // ==================== ACTIVITY TRACKING ====================
    setInterval(() => {
        axios.post('{{ route("track-activity") }}').catch(e => console.log(e));
    }, 240000);

    // ==================== INITIALIZATION ====================
    $(document).ready(function() {
        // Sidebar toggles
        $('#dismissPollSidebar, #dismissQuestionSidebar').on('click', function() {
            $('#pollSidebar, #questionSidebar').removeClass('active');
        });
        $('#pollSidebarCollapse').on('click', () => $('#pollSidebar').addClass('active'));
        $('#questionSidebarCollapse').on('click', () => $('#questionSidebar').addClass('active'));
        
        // Load initial data
        loadPoll();
        loadQuestions();
    });

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>

<style>
@keyframes floatUp {
    0% { transform: translateY(0) rotate(0deg); opacity: 1; }
    100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
}
</style>
@endpush