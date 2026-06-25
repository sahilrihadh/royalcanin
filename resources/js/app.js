import Alpine from "alpinejs";
import axios from "axios";
import "./echo";

// Globals
window.Alpine = Alpine;
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
    window.csrfToken = token.content;
}

// Helper: run callback when Echo is ready
window.__onEchoReady = function (callback) {
    if (window.Echo) {
        callback();
    } else {
        window.addEventListener("echo-ready", callback, { once: true });
    }
};

document.addEventListener("DOMContentLoaded", () => Alpine.start());
