<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Video Player</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: #000;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      overflow: hidden;
    }

    .video-container {
      width: 100%;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    video {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    /* Optional: Custom controls styling */
    video::-webkit-media-controls {
      z-index: 100;
    }
  </style>

  <!-- Include hls.js for cross-browser HLS support -->
  <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
</head>

<body>
  <div class="video-container">
    <video id="video" controls autoplay playsinline></video>
  </div>

  <script>
    const videoUrl =
      "https://customer-1x23yh7nrl2vignp.cloudflarestream.com/23c7e1423f0f42fee4154867f1c430b6/manifest/video.m3u8";
    const video = document.getElementById("video");

    // Check if HLS is supported
    if (Hls.isSupported()) {
      const hls = new Hls({
        // Optional: Configure for better performance
        enableWorker: true,
        lowLatencyMode: true,
        backBufferLength: 90,
      });

      hls.loadSource(videoUrl);
      hls.attachMedia(video);

      hls.on(Hls.Events.MANIFEST_PARSED, function() {
        console.log("Video loaded successfully");
        video.play().catch((e) => console.log("Autoplay prevented:", e));
      });

      hls.on(Hls.Events.ERROR, function(event, data) {
        console.error("HLS Error:", data);
        if (data.fatal) {
          switch (data.type) {
            case Hls.ErrorTypes.NETWORK_ERROR:
              console.log("Network error, trying to recover...");
              hls.startLoad();
              break;
            case Hls.ErrorTypes.MEDIA_ERROR:
              console.log("Media error, trying to recover...");
              hls.recoverMediaError();
              break;
            default:
              console.log("Fatal error, cannot recover");
              break;
          }
        }
      });
    }
    // For Safari which supports HLS natively
    else if (video.canPlayType("application/vnd.apple.mpegurl")) {
      video.src = videoUrl;
      video.addEventListener("loadedmetadata", function() {
        video.play().catch((e) => console.log("Autoplay prevented:", e));
      });
    } else {
      // Fallback message for unsupported browsers
      video.innerHTML =
        '<p style="color:white;text-align:center;">Your browser does not support video playback.</p>';
    }
  </script>
</body>

</html>