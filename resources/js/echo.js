import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher; // Required — Reverb uses the Pusher WS protocol

window.Echo = new Echo({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: parseInt(import.meta.env.VITE_REVERB_PORT ?? "8080"),
    wssPort: parseInt(import.meta.env.VITE_REVERB_PORT ?? "8080"),
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "http") === "https",
    enabledTransports: ["ws", "wss"],
    disableStats: true,
});

document.dispatchEvent(new CustomEvent("echo-ready"));
window.dispatchEvent(new CustomEvent("echo-ready"));
