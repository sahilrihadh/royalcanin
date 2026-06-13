// question.js
const questionChannel = pusher.subscribe("question-channel");
let questionRefreshInterval = null;

function initializeQuestions() {
    questionChannel.bind("new-question", function (data) {
        console.log("New question received:", data);
        showQuestion();
        showToast("New question has been posted!", "info");
    });

    showQuestion();

    if (questionRefreshInterval) clearInterval(questionRefreshInterval);
    questionRefreshInterval = setInterval(showQuestion, 30000);
}

async function showQuestion() {
    try {
        const response = await axios.post('{{ route("get-questions") }}', {
            request: 3,
        });

        $("#messages").html(response.data);
    } catch (error) {
        console.error("Error loading questions:", error);
    }
}

$("#question-form").validate({
    rules: {
        question_input: {
            required: true,
            minlength: 5,
        },
    },
    messages: {
        question_input: {
            required: "Please enter your question!",
            minlength: "Please enter at least 5 characters",
        },
    },
    submitHandler: async function (form) {
        const $form = $(form);
        const $submitBtn = $form.find('button[type="submit"]');
        const originalText = $submitBtn.text();
        const questionText = $form
            .find('textarea[name="question_input"]')
            .val();

        try {
            $submitBtn
                .prop("disabled", true)
                .html('<span class="loading-spinner"></span> Submitting...');

            const response = await axios.post($form.attr("action"), {
                question_input: questionText,
            });

            let message = "";
            if (response.data == 1) {
                message =
                    '<div class="alert alert-success">Question Submitted Successfully!</div>';
                $form[0].reset();
                await showQuestion();
                showToast("Your question has been submitted!", "success");
            } else if (response.data == 2) {
                message =
                    '<div class="alert alert-warning">You have already submitted a question!</div>';
            } else {
                message =
                    '<div class="alert alert-danger">Something went wrong!</div>';
            }

            $("#message").html(message).show();
            setTimeout(() => $("#message").fadeOut(), 3000);
        } catch (error) {
            console.error("Question submission error:", error);
            $("#message")
                .html(
                    '<div class="alert alert-danger">Failed to submit question. Please try again.</div>',
                )
                .show();
            setTimeout(() => $("#message").fadeOut(), 3000);
        } finally {
            $submitBtn.prop("disabled", false).text(originalText);
        }
    },
});
