@extends('layouts.master')

@section('title', 'Previous Sessions | Royal Canin')

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('fonts/font.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.2.0/css/glightbox.css">
<link href="{{ asset('assets/css/main.min.css') }}" rel="stylesheet">
<!-- Custom Fonts -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/font.css') }}" />
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
      <div class="doc-card">
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

    <div class="col-10 mt-4">
      <div class="accordion accordion-flush" id="faqAccordion">
        @foreach($faqs as $faq)
        <div class="accordion-item mb-3 border rounded-4 overflow-hidden">
          <h2 class="accordion-header" id="heading{{ $loop->iteration }}">
            <button class="accordion-button collapsed fw-semibold"
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
    webinar1: 'https://customer-1x23yh7nrl2vignp.cloudflarestream.com/ee1533a6e74c42a23064000a74bcc4b3/manifest/video.m3u8',
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

    const m3u8Ids = ['webinar1'];

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
    video.style.width = '100%';
    video.style.height = 'auto';
    video.style.borderRadius = '8px';

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