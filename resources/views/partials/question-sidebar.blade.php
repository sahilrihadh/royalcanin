<nav id="questionSidebar" class="sidebar-right">
  <div class="sidebar-header">
    <div id="dismissQuestionSidebar" class="dismiss dismiss-right">
      <i class="bi bi-arrow-right"></i>
    </div>
    <h3>ASK QUESTION</h3>
  </div>
  <div class="sidebar-body">
    <h4 class="text-dark sidebar-title">Please ask your questions, it'll be answered during Q&A session.</h4>
    <div class="question-wrapper">
      <div class="question-box mt-4 mt-md-0">
        <div id="message"></div>
        <form id="question-form" method="POST" action="{{ route('submit-question') }}">
          @csrf
          <textarea class="form-control input-rounded" name="question_input" id="question_input" rows="4" placeholder="Enter your question and click on submit!"></textarea>
          <div class="mt-3">
            <button type="submit" class="btn btn-canin">Submit Question</button>
          </div>
        </form>
      </div>

      <div class="view-question-box">
        <div class="chat_window">
          <div class="messages" id="messages"></div>
        </div>
      </div>
    </div>
  </div>
</nav>