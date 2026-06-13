// utils.js
function showToast(message, type = "info") {
    const toastHtml = `
        <div class="toast-notification alert alert-${type === "error" ? "danger" : type} alert-dismissible fade show" role="alert">
            <strong>${type === "error" ? "Error!" : type === "success" ? "Success!" : "Notice!"}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    $("body").append(toastHtml);

    setTimeout(() => {
        $(".toast-notification").fadeOut(300, function () {
            $(this).remove();
        });
    }, 3000);
}

function initializeSidebars() {
    $("#dismissPollSidebar, #dismissQuestionSidebar").on("click", function () {
        $("#pollSidebar, #questionSidebar").removeClass("active");
    });

    $("#pollSidebarCollapse").on("click", function () {
        $("#pollSidebar").addClass("active");
    });

    $("#questionSidebarCollapse").on("click", function () {
        $("#questionSidebar").addClass("active");
    });
}
