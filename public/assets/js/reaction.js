// reaction.js
function initializeReactions() {
    $("#loveBtn, #likeBtn, #applause").on("click", function () {
        const reactionType =
            this.id === "loveBtn"
                ? "love"
                : this.id === "likeBtn"
                  ? "like"
                  : "applause";
        createReactionAnimation(reactionType);
        storeReaction(reactionType);

        if (reactionType === "applause") {
            const audio = new Audio(
                '{{ asset("assets/audio/audience-clapping.mp3") }}',
            );
            audio.play().catch((e) => console.log("Audio play failed:", e));
        }
    });
}

function createReactionAnimation(type) {
    const heartContainer = document.getElementById("heartContainer");
    const count = 30;

    for (let i = 0; i < count; i++) {
        const element = document.createElement("div");
        element.classList.add(
            type === "love" ? "heart" : type === "like" ? "like" : "clap",
        );
        element.style.left = `${Math.random() * 100}vw`;
        element.style.bottom = `-${Math.random() * 10}vh`;
        const size = Math.random() * 30 + 10;
        element.style.width = `${size}px`;
        element.style.height = `${size}px`;
        element.style.animationDuration = `${Math.random() * 4 + 4}s`;
        element.style.opacity = Math.random();
        element.style.position = "fixed";
        element.style.zIndex = "9999";
        element.style.pointerEvents = "none";

        heartContainer.appendChild(element);
        element.addEventListener("animationend", () => element.remove());
    }
}

async function storeReaction(reactionType) {
    try {
        await axios.post('{{ route("store-reaction") }}', {
            reaction: reactionType,
        });
    } catch (error) {
        console.error("Error storing reaction:", error);
    }
}
