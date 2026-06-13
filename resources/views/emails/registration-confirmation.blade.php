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

    body {
      background-color: #f5f5f5;
      font-family: 'D-DIN Pro', 'Helvetica Neue', Arial, sans-serif;
      font-size: 15px;
      line-height: 1.6;
      margin: 0;
      padding: 20px 0;
      color: #1a1a1a;
      -webkit-text-size-adjust: 100%;
    }

    .wrapper {
      max-width: 620px;
      margin: 0 auto;
      background: #ffffff;
    }

    .header {
      background-color: #fefefe;
      padding: 36px 40px 30px;
      text-align: center;
    }

    .header img.logo {
      height: 52px;
      display: block;
      margin: 0 auto 20px;
    }

    .divider-line {
      width: 40px;
      height: 2px;
      background: #dc2626;
      margin: 0 auto 18px;
      border: none;
    }

    .series-label {
      font-size: 10px;
      font-weight: 400;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: #dc2626;
      margin: 0 0 10px;
    }

    .header-title {
      font-size: 44px;
      font-weight: 700;
      color: #dc2626;
      letter-spacing: 8px;
      margin: 0 0 8px;
      text-transform: uppercase;
      line-height: 1.1;
    }

    .header-subtitle {
      font-size: 12px;
      font-weight: 400;
      color: #dc2626;
      letter-spacing: 0.5px;
      margin: 0;
    }

    .hero-strip {
      background-color: #b91c1c;
      padding: 11px 40px;
      text-align: center;
    }

    .hero-strip p {
      font-size: 11px;
      font-weight: 500;
      color: rgba(255, 255, 255, 0.9);
      letter-spacing: 2px;
      text-transform: uppercase;
      margin: 0;
    }

    .body-section {
      padding: 36px 40px 28px;
    }

    .greeting {
      font-size: 13px;
      font-weight: 700;
      color: #dc2626;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      margin: 0 0 14px;
    }

    .body-section p {
      font-size: 15px;
      font-weight: 400;
      color: #333333;
      margin: 0 0 16px;
    }



    .schedule-header {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: #dc2626;
      margin: 32px 0 14px;
    }

    table.schedule {
      width: 100%;
      border-collapse: collapse;
    }

    table.schedule thead tr {
      background-color: #1a1a1a;
    }

    table.schedule thead th {
      padding: 12px 10px;
      text-align: left;
      font-weight: 700;
      font-size: 10px;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: #f5f5f5;
    }

    table.schedule tbody tr {
      border-bottom: 1px solid #e5e5e5;
    }

    table.schedule tbody tr.active {
      background-color: #fff5f5;
      border-left: 3px solid #dc2626;
    }

    table.schedule tbody tr.active td:first-child {
      border-left: 3px solid #dc2626;
      padding-left: 7px;
    }

    table.schedule tbody tr.active td {
      color: #7f1d1d;
      font-weight: 600;
      background-color: #fff5f5;
    }

    table.schedule tbody td {
      padding: 12px 10px;
      font-size: 13px;
      font-weight: 400;
      color: #444444;
      vertical-align: top;
    }

    table.schedule tbody td.topic {
      color: #1a1a1a;
    }

    .badge-active {
      display: inline-block;
      background: #dc2626;
      color: #ffffff;
      font-size: 8px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      padding: 2px 8px;
      border-radius: 2px;
      margin-left: 8px;
      vertical-align: middle;
    }

    .cta-section {
      padding: 28px 40px;
      text-align: center;
      background: #fff5f5;
      border-top: 1px solid #fecaca;
      border-bottom: 1px solid #fecaca;
    }

    .cta-section p {
      font-size: 14px;
      font-weight: 400;
      color: #7f1d1d;
      margin: 0 0 18px;
    }

    .cta-btn {
      display: inline-block;
      background-color: #dc2626;
      color: #ffffff !important;
      text-decoration: none;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 3px;
      text-transform: uppercase;
      padding: 14px 38px;
      border-radius: 2px;
    }

    .footer {
      padding: 28px 40px;
      text-align: center;
      border-top: 3px solid #dc2626;
    }

    .footer p {
      font-size: 13px;
      font-weight: 400;
      color: #888888;
      margin: 0 0 4px;
    }

    .sign-off {
      font-size: 16px;
      font-weight: 500;
      color: #dc2626;
      font-style: italic;
      margin: 0 0 16px;
      display: block;
    }

    @media (max-width: 640px) {

      .body-section,
      .footer,
      .cta-section {
        padding: 24px 20px;
      }

      .header {
        padding: 28px 20px 22px;
      }

      .header-title {
        font-size: 32px;
        letter-spacing: 5px;
      }

      table.schedule thead th,
      table.schedule tbody td {
        padding: 8px 6px;
        font-size: 11px;
      }

      table.schedule tbody td.topic {
        font-size: 11px;
      }
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
        <p class="header-subtitle">A Six-Part Series on Gastrointestinal Medicine</p>
      </td>
    </tr>
    <tr>
      <td class="hero-strip">
        <p>Congratulations! Your registration is confirmed</p>
      </td>
    </tr>
    <tr>
      <td class="body-section">
        <p class="greeting">Dear {{ $user->full_name }},</p>

        <p><strong>Congratulations!</strong> Your registration for the GI Horizons webinar series is successfully completed.</p>

        <p>We are excited to have you on-board for this exclusive six-part learning experience with Dr. K. G. Umesh.</p>



        <p class="schedule-header">Complete Series Schedule</p>
        <table class="schedule" role="presentation" cellspacing="0" cellpadding="0">
          <thead>
            <tr>
              <th>Date</th>
              <th>Day</th>
              <th>Topic</th>
              <th>Time (IST)</th>
            </tr>
          </thead>
          <tbody>
            @php
            $today = strtotime(date('Y-m-d'));
            $sessions = [
            ['date' => '27 May 2026', 'day' => 'Wednesday', 'topic' => 'When Angry Pancreas throws a tantrum', 'time' => '1 PM - 2 PM', 'timestamp' => strtotime('2026-05-27')],
            ['date' => '26 June 2026', 'day' => 'Friday', 'topic' => 'Hungry, hungry doggo - The EPI edition', 'time' => '7 PM - 8 PM', 'timestamp' => strtotime('2026-06-26')],
            ['date' => '22 Jul 2026', 'day' => 'Wednesday', 'topic' => 'Serial poopers - Loose stools, long tales', 'time' => '1 PM - 2 PM', 'timestamp' => strtotime('2026-07-22')],
            ['date' => '19 Aug 2026', 'day' => 'Wednesday', 'topic' => 'Acute diarrhoea - New tricks, Same mess', 'time' => '1 PM - 2 PM', 'timestamp' => strtotime('2026-08-19')],
            ['date' => '23 Sep 2026', 'day' => 'Wednesday', 'topic' => 'Liver under pressure - Let liver Live. Part 1', 'time' => '1 PM - 2 PM', 'timestamp' => strtotime('2026-09-23')],
            ['date' => '21 Oct 2026', 'day' => 'Wednesday', 'topic' => 'Liver under pressure - Let liver Live. Part 2', 'time' => '1 PM - 2 PM', 'timestamp' => strtotime('2026-10-21')]
            ];

            $nextSession = null;
            $nextSessionIndex = -1;
            foreach ($sessions as $index => $session) {
            if ($session['timestamp'] >= $today) {
            $nextSession = $session;
            $nextSessionIndex = $index;
            break;
            }
            }
            @endphp

            @foreach($sessions as $index => $session)
            <tr class="{{ $index === $nextSessionIndex ? 'active' : '' }}">
              <td>
                {{ $session['date'] }}
                @if($index === $nextSessionIndex)
                <span class="badge-active">Upcoming</span>
                @endif
              </td>
              <td>{{ $session['day'] }}</td>
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
        <p>Join the webinar portal to access the live session, study materials, and past recordings.</p>
        <a href="https://royalcanin.sociolive.in/" class="cta-btn">Access Webinar Portal</a>
      </td>
    </tr>
    <tr>
      <td class="footer">
        <span class="sign-off">Wishing you paw-some days ahead 🐾</span>
        <p>Warm regards,<br /><strong style="color:#1a1a1a; font-weight:700;">Team Royal Canin</strong></p>
        <p style="margin-top:14px; font-size:11px; color:#aaaaaa;">
          You are registered for the GI Horizons webinar series.<br />
          <a href="https://royalcanin.sociolive.in/" style="color:#dc2626; text-decoration:none;">royalcanin.sociolive.in</a> | <a href="https://www.mars.com/privacy" style="color:#dc2626; text-decoration:none;">Privacy Policy</a>
        </p>
      </td>
    </tr>
  </table>
</body>

</html>