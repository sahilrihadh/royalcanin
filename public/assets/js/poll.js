// poll.js
const pollChannel = pusher.subscribe("poll-channel");

pollChannel.bind("poll-status-changed", function (data) {
    console.log("Poll status changed:", data);

    if (data.status === "active" && data.pollHtml) {
        $("#poll").html(data.pollHtml);
        $("#pollSidebarCollapse").click();
        showToast("New poll available! Click to participate.", "info");
    } else if (data.status === "inactive") {
        $("#pollSidebar").removeClass("active");
        $("#poll").html(
            '<div class="alert alert-info">No active poll available at the moment.</div>',
        );
        showToast("Poll has ended. Thank you for participating!", "info");
    }
});

let pollRefreshInterval = null;
let isPollActive = false;

function initializePoll() {
    pollChannel.bind("poll-status-changed", function (data) {
        console.log("Poll status changed:", data);
        if (data.status === "active") {
            checkPoll();
        } else if (data.status === "inactive") {
            closePoll();
        }
    });

    checkPoll();
}

async function checkPoll() {
    try {
        const response = await axios.post('{{ route("check-poll") }}', {
            request: 1,
        });

        if (response.data === "NO_POLL_ACTIVE" || response.data === "") {
            closePoll();
        } else {
            showPoll(response.data);
        }
    } catch (error) {
        console.error("Error checking poll:", error);
    }
}

function showPoll(pollHtml) {
    $("#pollSidebarCollapse").click();
    $("#poll").html(pollHtml);
    isPollActive = true;
}

function closePoll() {
    $("#pollSidebar").removeClass("active");
    $("#poll").html(
        '<div class="alert alert-info">No active poll available at the moment.</div>',
    );
    isPollActive = false;
}

$(document).on("click", "#but_vote", async function (e) {
    e.preventDefault();

    const checkedPoll = $("#poll input[name='poll']:checked").val();

    if (!checkedPoll) {
        showToast("Please select an option.", "warning");
        return;
    }

    const $btn = $(this);
    const originalText = $btn.text();

    try {
        $btn.prop("disabled", true).text("Submitting...");

        const response = await axios.post('{{ route("submit-vote") }}', {
            request: 2,
            poll: checkedPoll,
        });

        if (response.data == 1) {
            showToast("Vote submitted successfully!", "success");
            await checkPoll();
        } else {
            showToast("Failed to submit vote. Please try again.", "error");
        }
    } catch (error) {
        console.error("Vote submission error:", error);
        showToast("An error occurred. Please try again.", "error");
    } finally {
        $btn.prop("disabled", false).text(originalText);
    }
});
