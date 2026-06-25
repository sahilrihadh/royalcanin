<?php $__env->startSection('title', 'Webcast | Royal Canin'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://unpkg.com/simplebar@latest/dist/simplebar.css">
<link href="<?php echo e(asset('assets/css/main.min.css')); ?>?v=<?php echo e(time()); ?>" rel="stylesheet">
<style>
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
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes floatUp {
        0% { transform: translateY(0) rotate(0deg); opacity: 1; }
        100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
    }
    .btn-loading { opacity: 0.7; pointer-events: none; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-8 col-12">
            <div class="ratio ratio-16x9">
                <iframe src="<?php echo e(asset('player.php')); ?>" frameborder="0"
                    allow="autoplay; fullscreen; picture-in-picture"
                    allowfullscreen
                    style="position:absolute;top:0;left:0;width:100%;height:100%;">
                </iframe>
            </div>
        </div>
    </div>
</div>

<div class="sidebar-btn sidebar-btn-left" id="pollSidebarCollapse">
    <img src="<?php echo e(asset('assets/img/poll.png')); ?>" class="img-fluid" alt="Poll">
    <span class="blob"></span>
    <div>ANSWER THE POLL</div>
</div>

<div class="sidebar-btn sidebar-btn-right" id="questionSidebarCollapse">
    <span class="blob"></span>
    <img src="<?php echo e(asset('assets/img/ask-questio.png')); ?>" class="img-fluid" alt="Question">
    <div>ASK A QUESTION</div>
</div>

<?php echo $__env->make('partials.reactions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.poll-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.question-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.announcement-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://unpkg.com/simplebar@latest/dist/simplebar.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ==================== CONFIG ====================
    const csrfToken = '<?php echo e(csrf_token()); ?>';
    const ROUTES = {
        checkPoll:      '<?php echo e(route("check.poll")); ?>',
        submitPollVote: '<?php echo e(route("submit.poll.vote")); ?>',
        getQuestions:   '<?php echo e(route("get-questions")); ?>',
        submitQuestion: '<?php echo e(route("submit-question")); ?>',
        storeReaction:  '<?php echo e(route("store-reaction")); ?>',
        trackActivity:  '<?php echo e(route("track-activity")); ?>',
    };
    const APPLAUSE_URL = '<?php echo e(asset("assets/audio/audience-clapping.mp3")); ?>';

    // axios already configured in app.js, but set csrf just in case
    if (window.axios) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
    }

    // ==================== UTILS ====================
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showToast(title, message, type = 'success') {
        if (!$('#toastContainer').length) {
            $('body').append('<div id="toastContainer" style="position:fixed;top:20px;right:20px;z-index:9999;"></div>');
        }
        const id = 'toast_' + Date.now();
        const colors = { success: '#28a745', error: '#dc3545', info: '#17a2b8' };
        const icons  = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle' };
        $('#toastContainer').append(`
            <div id="${id}" class="toast show" role="alert" style="min-width:300px;margin-bottom:10px;">
                <div class="toast-header" style="background:${colors[type]};color:white;">
                    <i class="fas ${icons[type]} me-2"></i>
                    <strong class="me-auto">${escapeHtml(title)}</strong>
                    <small>Just now</small>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">${escapeHtml(message)}</div>
            </div>
        `);
        setTimeout(() => $(`#${id}`).fadeOut(300, function() { $(this).remove(); }), 5000);
    }

    // ==================== ANNOUNCEMENTS ====================
    function initAnnouncements() {
        window.Echo.channel('announcements')
            .listen('.show-announcement', (data) => {
                console.log('📢 Announcement:', data);
                document.getElementById('announcementModalBody').innerHTML = `
                    <div class="text-center mb-3">
                        <i class="fas fa-bullhorn" style="font-size:48px;color:#dc2626;"></i>
                    </div>
                    <h4 class="text-danger text-center mb-3">${escapeHtml(data.title)}</h4>
                    <p class="text-center mb-0">${escapeHtml(data.description)}</p>
                    <hr>
                    <small class="text-muted d-block text-center">${new Date().toLocaleString()}</small>
                `;
                new bootstrap.Modal(document.getElementById('announcementModal')).show();
            })
            .listen('.hide-announcement', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('announcementModal'));
                if (modal) modal.hide();
            });
    }

    // ==================== POLL ====================
    let isPollSubmitting = false;

    function loadPoll() {
        axios.post(ROUTES.checkPoll, { request: 1 })
            .then(res => {
                if (res.data?.has_poll && res.data.poll?.html) {
                    $('#poll').html(res.data.poll.html);
                    $('#pollSidebar').addClass('active');
                } else {
                    $('#pollSidebar').removeClass('active');
                    $('#poll').html('<div class="alert alert-info">No active poll available.</div>');
                }
            })
            .catch(err => console.error('Poll error:', err));
    }

    function initPoll() {
        window.Echo.channel('poll-channel')
            .listen('.poll-status-changed', () => {
                console.log('🗳️ Poll updated');
                loadPoll();
            });
    }

    $(document).on('click', '#but_vote', async function(e) {
        e.preventDefault();
        if (isPollSubmitting) return;

        const selectedOption = $("#poll input[name='poll']:checked").val();
        if (!selectedOption) { alert('Please select an option.'); return; }

        isPollSubmitting = true;
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="loading-spinner"></span> Submitting...');

        try {
            const res = await axios.post(ROUTES.submitPollVote, {
                request: 2,
                poll: selectedOption,
                poll_id: $("#poll input[name='poll_id']").val()
            });
            if (res.data?.success || res.data === 1) {
                showToast('Success', 'Vote submitted successfully!', 'success');
                loadPoll();
            } else {
                showToast('Error', res.data?.message || 'Failed to submit vote', 'error');
                $btn.prop('disabled', false).html(originalHtml);
            }
        } catch {
            showToast('Error', 'Error submitting vote', 'error');
            $btn.prop('disabled', false).html(originalHtml);
        } finally {
            isPollSubmitting = false;
        }
    });

    // ==================== QUESTIONS ====================
    let isQuestionSubmitting = false;

    function loadQuestions() {
        axios.post(ROUTES.getQuestions, { request: 3 })
            .then(res => {
                if (res.data && res.data !== '') {
                    $('#messages').html(res.data);
                    const el = document.getElementById('messages');
                    if (el) el.scrollTop = el.scrollHeight;
                } else {
                    $('#messages').html('<div class="text-center text-muted p-4">No questions yet. Be the first to ask!</div>');
                }
            })
            .catch(() => {
                $('#messages').html('<div class="text-center text-danger p-4">Error loading questions. Please refresh.</div>');
            });
    }

    function submitUserQuestion() {
        if (isQuestionSubmitting) return;
        const questionInput = document.getElementById('question_input');
        const question = questionInput?.value.trim();
        if (!question) { showToast('Error', 'Please enter your question.', 'error'); return; }

        isQuestionSubmitting = true;
        const submitBtn = document.getElementById('submitQuestionBtn');
        const originalHtml = submitBtn?.innerHTML ?? 'Submit';
        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<span class="loading-spinner"></span> Submitting...'; }

        fetch(ROUTES.submitQuestion, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ question_input: question })
        })
        .then(r => r.json())
        .then(data => {
            if (data === 1 || data.success === true) {
                questionInput.value = '';
                showToast('Success', 'Your question has been submitted!', 'success');
                loadQuestions();
            } else if (data === 2) {
                showToast('Info', 'You already submitted a question. Please wait for it to be answered.', 'info');
            } else {
                showToast('Error', 'Failed to submit question. Please try again.', 'error');
            }
        })
        .catch(() => showToast('Error', 'Something went wrong. Please try again.', 'error'))
        .finally(() => {
            isQuestionSubmitting = false;
            if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalHtml; }
        });
    }

    function initQuestions() {
        window.Echo.channel('question-channel')
            .listen('.question-answered', (data) => {
                console.log('✅ Question answered:', data);
                showToast('Question Answered', `${escapeHtml(data.user_name)}'s question has been answered!`, 'info');
                loadQuestions();
            })
            .listen('.new-question', () => {
                console.log('❓ New question');
                loadQuestions();
            });
    }

    // ==================== REACTIONS ====================
    let submittedReactions = JSON.parse(sessionStorage.getItem('submittedReactions') || '{}');
    let reactionTimer = null;

    function initReactions() {
        $(document).on('click', '.reaction-btn', function() {
            const type = $(this).data('reaction');
            createReactionAnimation(type);
            if (type === 'applause') playApplause();
            if (submittedReactions[type] || reactionTimer) return;

            reactionTimer = setTimeout(() => reactionTimer = null, 3000);
            submittedReactions[type] = true;
            sessionStorage.setItem('submittedReactions', JSON.stringify(submittedReactions));
            axios.post(ROUTES.storeReaction, { reaction: type }).catch(err => console.error(err));
        });
    }

    function createReactionAnimation(type) {
        const container = document.getElementById('heartContainer');
        if (!container) return;
        for (let i = 0; i < 30; i++) {
            const el = document.createElement('div');
            el.className = type === 'love' ? 'heart' : (type === 'like' ? 'like' : 'clap');
            el.style.cssText = `position:fixed;left:${Math.random()*100}vw;bottom:-20px;
                width:${Math.random()*30+10}px;height:${Math.random()*30+10}px;
                animation:floatUp ${Math.random()*4+4}s ease-out forwards;z-index:9999;pointer-events:none;`;
            container.appendChild(el);
            el.addEventListener('animationend', () => el.remove());
        }
    }

    function playApplause() {
        new Audio(APPLAUSE_URL).play().catch(() => {});
    }

    // ==================== BOOT ====================
    function bootAll() {
        // Sidebar toggles
        $('#pollSidebarCollapse').on('click',     () => $('#pollSidebar').addClass('active'));
        $('#questionSidebarCollapse').on('click',  () => $('#questionSidebar').addClass('active'));
        $('#dismissPollSidebar, #dismissQuestionSidebar').on('click', function() {
            $('#pollSidebar, #questionSidebar').removeClass('active');
        });

        // Submit button + enter key
        const submitBtn = document.getElementById('submitQuestionBtn');
        if (submitBtn) submitBtn.addEventListener('click', (e) => { e.preventDefault(); submitUserQuestion(); });

        const questionInput = document.getElementById('question_input');
        if (questionInput) {
            questionInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); submitUserQuestion(); }
            });
        }

        // Load initial data
        loadPoll();
        loadQuestions();

        // Activity ping
        setInterval(() => axios.post(ROUTES.trackActivity).catch(() => {}), 240000);

        // Init Echo listeners
        initAnnouncements();
        initPoll();
        initQuestions();
        initReactions();
    }

    // Wait for Echo, then boot everything
    if (window.Echo) {
        $(document).ready(bootAll);
    } else {
        window.addEventListener('echo-ready', () => $(document).ready(bootAll), { once: true });
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sahilrihadh/Development/royalcanin/webinar-solution/resources/views/pages/webcast.blade.php ENDPATH**/ ?>