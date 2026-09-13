<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    @font-face {
      font-family: 'D-DIN Pro';
      src: url('https://royalcanin.sociolive.in/fonts/D-DIN-PRO-Regular.woff2') format('woff2'),
           url('https://royalcanin.sociolive.in/fonts/D-DIN-PRO-Regul.woff') format('woff');
      font-weight: 400;
      font-style: normal;
    }
    @font-face {
      font-family: 'D-DIN Pro';
      src: url('https://royalcanin.sociolive.in/fonts/D-DIN-PRO-Medium.woff2') format('woff2'),
           url('https://royalcanin.sociolive.in/fonts/D-DIN-PRO-Medi.woff') format('woff');
      font-weight: 500;
      font-style: normal;
    }
    @font-face {
      font-family: 'D-DIN Pro';
      src: url('https://royalcanin.sociolive.in/fonts/D-DIN-PRO-Bold.woff2') format('woff2'),
           url('https://royalcanin.sociolive.in/fonts/D-DIN-PRO-Bold.woff') format('woff');
      font-weight: 700;
      font-style: normal;
    }
    body { background-color: #f5f5f5; font-family: 'D-DIN Pro', 'Helvetica Neue', Arial, sans-serif; font-size: 15px; line-height: 1.6; margin: 0; padding: 20px 0; color: #1a1a1a; -webkit-text-size-adjust: 100%; }
    .wrapper { max-width: 620px; margin: 0 auto; background: #ffffff; }
    .header { background-color: #fefefe; padding: 36px 40px 30px; text-align: center; }
    .header img.logo { height: 52px; display: block; margin: 0 auto 20px; }
    .divider-line { width: 40px; height: 2px; background: #dc2626; margin: 0 auto 18px; border: none; }
    .series-label { font-size: 10px; font-weight: 400; letter-spacing: 3px; text-transform: uppercase; color: #dc2626; margin: 0 0 10px; }
    .header-title { font-size: 44px; font-weight: 700; color: #dc2626; letter-spacing: 8px; margin: 0 0 8px; text-transform: uppercase; line-height: 1.1; }
    .header-subtitle { font-size: 12px; font-weight: 400; color: #dc2626; letter-spacing: 0.5px; margin: 0; }
    .hero-strip { background-color: #b91c1c; padding: 11px 40px; text-align: center; }
    .hero-strip p { font-size: 11px; font-weight: 500; color: rgba(255,255,255,0.9); letter-spacing: 2px; text-transform: uppercase; margin: 0; }
    .body-section { padding: 36px 40px 28px; }
    .greeting { font-size: 13px; font-weight: 700; color: #dc2626; text-transform: uppercase; letter-spacing: 1.5px; margin: 0 0 14px; }
    .body-section p { font-size: 15px; font-weight: 400; color: #333333; margin: 0 0 16px; }
    .speaker-box { border-left: 3px solid #dc2626; padding: 14px 20px; background: #fff5f5; margin: 24px 0; }
    .speaker-box p { margin: 0; font-size: 14px; font-weight: 400; color: #7f1d1d; }
    .speaker-name { font-size: 16px; font-weight: 700; color: #991b1b; display: block; margin-bottom: 6px; letter-spacing: 0.5px; }
    .schedule-header { font-size: 10px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: #dc2626; margin: 32px 0 14px; }
    table.schedule { width: 100%; border-collapse: collapse; }
    table.schedule thead tr { background-color: #1a1a1a; }
    table.schedule thead th { padding: 11px 12px; text-align: left; font-weight: 700; font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: #f5f5f5; }
    table.schedule tbody tr { border-bottom: 1px solid #fee2e2; }
    table.schedule tbody tr.highlight { background-color: #fff5f5; }
    table.schedule tbody tr.highlight td { color: #7f1d1d; font-weight: 700; }
    table.schedule tbody td { padding: 11px 12px; font-size: 13px; font-weight: 400; color: #444444; vertical-align: top; }
    table.schedule tbody td.topic { font-style: italic; color: #1a1a1a; }
    .badge-next { display: inline-block; background: #dc2626; color: #ffffff; font-size: 9px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; padding: 2px 7px; border-radius: 2px; vertical-align: middle; margin-left: 6px; }
    .cta-section { padding: 28px 40px; text-align: center; background: #fff5f5; border-top: 1px solid #fecaca; border-bottom: 1px solid #fecaca; }
    .cta-section p { font-size: 14px; font-weight: 400; color: #7f1d1d; margin: 0 0 18px; }
    .cta-btn { display: inline-block; background-color: #dc2626; color: #ffffff !important; text-decoration: none; font-size: 11px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; padding: 14px 38px; border-radius: 2px; }
    .footer { padding: 28px 40px; text-align: center; border-top: 3px solid #dc2626; }
    .footer p { font-size: 13px; font-weight: 400; color: #888888; margin: 0 0 4px; }
    .sign-off { font-size: 16px; font-weight: 500; color: #dc2626; font-style: italic; margin: 0 0 16px; display: block; }
    @media (max-width: 640px) {
      .body-section, .footer, .cta-section { padding: 24px 20px; }
      .header { padding: 28px 20px 22px; }
      .header-title { font-size: 32px; letter-spacing: 5px; }
      table.schedule thead th, table.schedule tbody td { padding: 8px; font-size: 12px; }
    }
  </style>
</head>
<body>
  <table class="wrapper" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="620">
    <tr>
      <td class="header">
        <img class="logo" src="https://royalcanin.sociolive.in/assets/img/rc-logo.png" alt="Royal Canin" />
        <hr class="divider-line" />
        <p class="series-label">Royal Canin Webinar Series 2026</p>
        <h1 class="header-title">GI&nbsp;Horizons</h1>
        <p class="header-subtitle">A Six-Part Series on Gastrointestinal Medicine in Small Animals</p>
      </td>
    </tr>
    <tr>
      <td class="hero-strip">
        <p>Evidence-based updates for everyday practice &nbsp;&middot;&nbsp; Dr. K. G. Umesh</p>
      </td>
    </tr>
    <tr>
      <td class="body-section">
        <p class="greeting">Dear {{ $user->full_name }},</p>
        <p>This is a warm reminder that your next <em>GI Horizons</em> webinar session is coming up soon. We are delighted to have you with us for this exclusive series and look forward to an insightful session together.</p>
        <div class="speaker-box">
          <p>
            <span class="speaker-name">Dr. K. G. Umesh</span>
            Distinguished speaker for all six sessions. With decades of expertise in small animal medicine and nutrition, Dr. Umesh delivers evidence-based insights, case discussions, and clinical strategies to elevate veterinary practice.
          </p>
        </div>
        <p>Your upcoming session and the full series schedule are listed below. Study materials and recordings from previous sessions are available at the webinar portal under the <em>Study Material</em> and <em>Previous Sessions</em> tabs.</p>
        <p class="schedule-header">Series Schedule</p>
        <table class="schedule" role="presentation" cellspacing="0" cellpadding="0">
          <thead>
            <tr>
              <th>Date</th>
              <th>Topic</th>
              <th>Time (IST)</th>
            </tr>
          </thead>
          <tbody>
            @foreach($sessions as $session)
            <tr class="{{ $session['date'] === $targetSession['date'] ? 'highlight' : '' }}">
              <td>
                {{ $session['date'] }}
                @if($session['date'] === $targetSession['date'])
                <span class="badge-next">Reminder</span>
                @endif
              </td>
              <td class="topic">{{ $session['topic'] }}</td>
              <td>{{ $session['time'] }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </td>
    </tr>
    <tr>
      <td class="cta-section">
        <p>Join us on the webinar portal to access the live session, past recordings, and study materials.</p>
        <a href="https://royalcanin.sociolive.in/" class="cta-btn">Access Webinar Portal</a>
      </td>
    </tr>
    <tr>
      <td class="footer">
        <span class="sign-off">Wishing you paw-some days ahead &#128062;</span>
        <p>Warm regards,<br /><strong style="color:#1a1a1a; font-weight:700;">Team Royal Canin</strong></p>
        <p style="margin-top:14px; font-size:11px; color:#aaaaaa;">
          You are receiving this because you are registered for the GI Horizons webinar series.<br />
          Royal Canin India &nbsp;&middot;&nbsp; <a href="https://royalcanin.sociolive.in/" style="color:#dc2626; text-decoration:none;">royalcanin.sociolive.in</a>
        </p>
      </td>
    </tr>
  </table>
</body>
</html>
