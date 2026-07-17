<!-- Previous Session Reminder Modal -->
<div class="modal fade" id="previousSessionModal" tabindex="-1" aria-labelledby="previousSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previousSessionModalLabel"><i class="fas fa-play-circle me-2"></i>Missed a Session?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Catch up anytime! Head over to the <strong>Previous Session</strong> tab to:</p>
                <ul class="mb-0">
                    <li>Watch recordings of our earlier webinars</li>
                    <li>Download the study material shared during those sessions</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Maybe Later</button>
                <a href="{{ route('previous-sessions') }}" class="btn btn-canin">Go to Previous Session</a>
            </div>
        </div>
    </div>
</div>