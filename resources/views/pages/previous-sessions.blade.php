@extends('layouts.master')

@section('title', 'Previous Sessions | Royal Canin')

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('fonts/font.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.2.0/css/glightbox.css">
<link href="{{ asset('assets/css/main.min.css') }}" rel="stylesheet">
<!-- Custom Fonts -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/font.css') }}" />

<style>
  /* Reserve the thumbnail's own 16:9 aspect ratio up front so the video
     player doesn't briefly render smaller than the thumbnail while the
     stream's metadata is still loading, then jump to full size. */
  .video-container {
    aspect-ratio: 16 / 9;
  }

  .video-container video {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 8px;
  }

  .session-info h2 {
    font-family: "D-DIN-PRO";
    font-size: 24px;
    font-weight: 700;
    text-transform: uppercase;
    color: #e2001a;
    margin-bottom: 10px;
  }

  .session-info h3 {
    font-family: "D-DIN-PRO";
    font-size: 18px;
    font-weight: 600;
    text-transform: uppercase;
    color: #e2001a;
    margin-bottom: 10px;
  }

  .session-info .date-wrapper h4 {
    font-size: 14px;
    color: #666;
    font-weight: 400;
  }

  .faq-eyebrow {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #e2001a;
    margin-bottom: 6px;
  }

  .faq-heading {
    font-family: "D-DIN-PRO";
    font-weight: 700;
    font-size: 28px;
    color: #1a1a1a;
    margin-bottom: 0;
  }

  .faq-section .accordion-item {
    border: 1px solid #ececec;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  }

  .faq-section .accordion-button {
    font-family: "D-DIN-PRO";
    font-weight: 600;
    font-size: 15.5px;
    padding: 16px 20px;
    background-color: #fff;
    color: #1a1a1a;
    box-shadow: none;
    transition: background-color .15s ease, color .15s ease;
  }

  .faq-section .accordion-button:hover {
    background-color: #fff5f5;
  }

  .faq-section .accordion-button:focus {
    box-shadow: 0 0 0 3px rgba(226, 0, 26, 0.15);
  }

  .faq-section .accordion-button:not(.collapsed) {
    background-color: #e2001a;
    color: #fff;
  }

  .faq-section .accordion-button:not(.collapsed)::after {
    filter: brightness(0) invert(1);
  }

  .faq-section .accordion-body {
    padding: 4px 20px 22px;
    font-size: 14.5px;
    color: #444;
    line-height: 1.7;
  }

  .faq-section .accordion-body ul {
    padding-left: 20px;
    margin-bottom: 0;
  }
</style>
@endpush

@section('content')
<div class="container mt-4">
  <!-- Webinar 1 - Convert to m3u8 -->
  <div class="row justify-content-center mb-lg-5 mb-md-4 mb-4">
    <div class="col-lg-4 mb-12 glit-grid align-self-center text-center">
      <!-- Custom m3u8 player -->
      <div class="video-thumbnail" onclick="playVideo('webinar1', event)">
        <img src="{{ asset('assets/img/video-thumb.jpg') }}" class="img-fluid" alt="Webinar 1 Thumbnail" />
        <div class="play-button-overlay"><i class="bi bi-play-fill"></i></div>
      </div>
      <!-- Hidden video container for m3u8 -->
      <div id="webinar1-player" class="video-container" style="display: none;"></div>
    </div><!-- end col-->

    <div class="col-lg-6 col-md-12 col-12 align-self-center mt-lg-0 mt-md-4 mt-4">
      <div class="session-info">
        <h2>WEBINAR 1</h2>
        <h3>When Angry Pancreas throws a tantrum</h3>
        <div class="date-wrapper">
          <h4>Date : 27<sup>th</sup> May 2026, 01:00 pm (IST)</h4>
          <h4>
            <a href="https://royalcanin.sociolive.in/assets/notes/Pancreatitis_Umesh_March_2026.pdf" download class="btn btn-canin mt-3" target="_blank">
              Download Notes <i class="bi bi-download ms-2"></i>
            </a>
          </h4>
        </div>
      </div>
    </div><!-- end col-->

  </div><!-- end row -->

  <div class="row justify-content-center mb-lg-5 mb-md-4 mb-4">
    <div class="col-lg-9 col-md-11 col-12">
      <div class="faq-section">
        <div class="text-center mb-4">
          <p class="faq-eyebrow">Session 1 &middot; Pancreatitis</p>
          <h2 class="faq-heading">Questions from Webinar 1</h2>
        </div>
        <div class="accordion accordion-flush" id="faqAccordion">
          @foreach($faqs as $faq)
          <div class="accordion-item mb-3">
            <h2 class="accordion-header" id="heading{{ $loop->iteration }}">
              <button class="accordion-button collapsed"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapse{{ $loop->iteration }}">
                {{ $faq['question'] }}
              </button>
            </h2>
            <div id="collapse{{ $loop->iteration }}"
              class="accordion-collapse collapse"
              data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                {!! $faq['answer'] !!}
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div><!-- end row -->

  <div class="row justify-content-center mb-lg-5 mb-md-4 mb-4">
        <div class="col-lg-4 mb-12 glit-grid align-self-center text-center">
        <!-- Replace with custom m3u8 player -->
        <div class="video-thumbnail" onclick="playVideo('webinar2', event)">
            <img src="assets/img/video-thumb.jpg" class="img-fluid" alt="Webinar 2 Thumbnail" />
            <div class="play-button-overlay">▶</div>
        </div>
        <!-- Hidden video container for m3u8 -->
        <div id="webinar2-player" class="video-container" style="display: none;"></div>
        </div><!-- end col-->
    <div class="col-lg-6 col-md-12 col-12 align-self-center mt-lg-0 mt-md-4 mt-4">
      <div class="session-info">
        <h2>WEBINAR 2</h2>
        <h3>Hungry, hungry doggo- The EPI edition</h3>
        <div class="date-wrapper">
          <h4>Date : 26<sup>th</sup> June 2026, 07:00 pm (IST)</h4>
          <!-- <h4><a href="https://royalcanin.sociolive.in/assets/notes/Pancreatitis_Umesh_March_2026.pdf" download class="btn btn-success mt-3" target="_blank">Download Notes</a></h4> -->
        </div>
      </div>
    </div><!-- end col-->
  </div><!-- end row -->

  <div class="row justify-content-center mb-lg-5 mb-md-4 mb-4">
    <div class="col-lg-4 mb-12 glit-grid align-self-center text-center">
      <div class="video-thumbnail" onclick="playVideo('webinar3', event)">
        <img src="{{ asset('assets/img/video-thumb.jpg') }}" class="img-fluid" alt="Webinar 3 Thumbnail" />
        <div class="play-button-overlay"><i class="bi bi-play-fill"></i></div>
      </div>
      <div id="webinar3-player" class="video-container" style="display: none;"></div>
    </div><!-- end col-->

    <div class="col-lg-6 col-md-12 col-12 align-self-center mt-lg-0 mt-md-4 mt-4">
      <div class="session-info">
        <h2>WEBINAR 3</h2>
        <h3>Serial poopers - Loose stools, long tales</h3>
        <div class="date-wrapper">
          <h4>Date : 22<sup>nd</sup> July 2026, 01:00 pm (IST)</h4>
          <!-- <h4><a href="#" download class="btn btn-canin mt-3" target="_blank">Download Notes</a></h4> -->
        </div>
      </div>
    </div><!-- end col-->
  </div><!-- end row -->

  <div class="row justify-content-center mb-lg-5 mb-md-4 mb-4">
    <div class="col-lg-4 mb-12 glit-grid align-self-center text-center">
      <div class="video-thumbnail" onclick="playVideo('webinar4', event)">
        <img src="{{ asset('assets/img/video-thumb.jpg') }}" class="img-fluid" alt="Webinar 4 Thumbnail" />
        <div class="play-button-overlay"><i class="bi bi-play-fill"></i></div>
      </div>
      <div id="webinar4-player" class="video-container" style="display: none;"></div>
    </div><!-- end col-->

    <div class="col-lg-6 col-md-12 col-12 align-self-center mt-lg-0 mt-md-4 mt-4">
      <div class="session-info">
        <h2>WEBINAR 4</h2>
        <h3>Acute diarrhoea - New tricks, Same mess</h3>
        <div class="date-wrapper">
          <h4>Date : 19<sup>th</sup> August 2026, 01:00 pm (IST)</h4>
          <!-- <h4><a href="#" download class="btn btn-canin mt-3" target="_blank">Download Notes</a></h4> -->
        </div>
      </div>
    </div><!-- end col-->
  </div><!-- end row -->
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.2.0/js/glightbox.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

<script>
  // Video URLs for m3u8 videos
  const m3u8Urls = {
    webinar1: 'https://customer-1x23yh7nrl2vignp.cloudflarestream.com/78de229569106dfae08b4728c0be13a8/manifest/video.m3u8',
    webinar2: 'https://customer-1x23yh7nrl2vignp.cloudflarestream.com/e90433a288c3294c28ea6b36227a61ff/manifest/video.m3u8',
    webinar3: 'https://customer-1x23yh7nrl2vignp.cloudflarestream.com/215f7f14b838032b0276279335635fcd/manifest/video.m3u8',
    webinar4: 'https://customer-1x23yh7nrl2vignp.cloudflarestream.com/3b82f3dd62bf9cd5ff7c938f3d055805/manifest/video.m3u8',
  };

  // Store active HLS instances
  let activeHlsInstances = [];
  let currentlyPlayingId = null;

  // Function to reset m3u8 videos
  function resetM3u8Videos(exceptId = null) {
    activeHlsInstances.forEach(hls => {
      if (hls) {
        hls.destroy();
      }
    });
    activeHlsInstances = [];

    const m3u8Ids = ['webinar1', 'webinar2', 'webinar3', 'webinar4'];

    m3u8Ids.forEach(id => {
      if (id !== exceptId) {
        const container = document.getElementById(id + '-player');
        const thumbnail = container?.parentElement?.querySelector('.video-thumbnail');

        if (container) {
          const video = container.querySelector('video');
          if (video) {
            video.pause();
            video.src = '';
            video.load();
          }
          container.style.display = 'none';
          container.innerHTML = '';
        }

        if (thumbnail) {
          thumbnail.style.display = 'block';
        }
      }
    });

    currentlyPlayingId = exceptId;
  }

  // Function to play m3u8 video
  function playVideo(webinarId, event) {
    event.preventDefault();
    event.stopPropagation();

    const thumbnail = event.currentTarget;
    const container = document.getElementById(webinarId + '-player');
    const videoUrl = m3u8Urls[webinarId];

    if (!videoUrl) {
      alert('Video not available yet. Please check back later.');
      return;
    }

    if (currentlyPlayingId === webinarId) {
      return;
    }

    resetM3u8Videos(webinarId);

    thumbnail.style.display = 'none';
    container.style.display = 'block';

    const video = document.createElement('video');
    video.controls = true;

    container.appendChild(video);

    if (Hls.isSupported()) {
      const hls = new Hls();
      hls.loadSource(videoUrl);
      hls.attachMedia(video);
      activeHlsInstances.push(hls);

      hls.on(Hls.Events.MANIFEST_PARSED, function() {
        video.play();
      });

      hls.on(Hls.Events.ERROR, function(event, data) {
        if (data.fatal) {
          console.error('HLS error:', data);
          container.style.display = 'none';
          container.innerHTML = '';
          thumbnail.style.display = 'block';
          currentlyPlayingId = null;
        }
      });
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
      video.src = videoUrl;
      video.addEventListener('loadedmetadata', function() {
        video.play();
      });
    } else {
      container.innerHTML = '<p>Your browser does not support HLS video playback.</p>';
      setTimeout(() => {
        thumbnail.style.display = 'block';
        container.style.display = 'none';
        currentlyPlayingId = null;
      }, 3000);
    }

    video.addEventListener('ended', function() {
      container.style.display = 'none';
      container.innerHTML = '';
      thumbnail.style.display = 'block';
      currentlyPlayingId = null;
    });

    // Send certificate via AJAX
    let email = "{{ Auth::user()->email_id ?? '' }}";
    let fullName = "{{ Auth::user()->full_name ?? '' }}";
    let currentWebinarId = webinarId;

    $.ajax({
      url: '{{ route("send-certificate") }}',
      type: 'POST',
      data: {
        email: email,
        fullName: fullName,
        webinarId: currentWebinarId,
        _token: '{{ csrf_token() }}'
      },
      success: function(response) {
        console.log('Certificate sent successfully:', response);
      },
      error: function(xhr, status, error) {
        console.error('Error sending certificate:', error);
      }
    });
  }

  // Initialize GLightbox for webinar 3 (Vimeo)
  var lightboxVideo = GLightbox({
    selector: '.glightbox3'
  });

  // Clean up on page unload
  window.addEventListener('beforeunload', function() {
    resetM3u8Videos();
  });
</script>
@endpush