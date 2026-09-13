<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    body {
      background-color: #f5f5f5;
      font-family: 'Helvetica Neue', Arial, sans-serif;
      font-size: 15px;
      line-height: 1.6;
      margin: 0;
      padding: 20px 0;
      color: #1a1a1a;
      -webkit-text-size-adjust: 100%;
    }

    .wrapper {
      max-width: 480px;
      margin: 0 auto;
      background: #ffffff;
    }

    .header {
      background-color: #fefefe;
      padding: 32px 40px 24px;
      text-align: center;
    }

    .header img.logo {
      height: 48px;
      display: block;
      margin: 0 auto;
    }

    .divider-line {
      width: 40px;
      height: 2px;
      background: #dc2626;
      margin: 20px auto 0;
      border: none;
    }

    .body-section {
      padding: 8px 40px 32px;
      text-align: center;
    }

    .body-section p {
      font-size: 15px;
      color: #333333;
      margin: 0 0 16px;
      text-align: left;
    }

    .otp-box {
      display: inline-block;
      background-color: #fef2f2;
      border: 1px solid #dc2626;
      border-radius: 6px;
      padding: 16px 32px;
      margin: 8px 0 20px;
    }

    .otp-code {
      font-size: 32px;
      font-weight: 700;
      letter-spacing: 8px;
      color: #dc2626;
    }

    .expiry-note {
      font-size: 13px;
      color: #777777;
      text-align: left;
    }

    .footer {
      background-color: #1a1a1a;
      padding: 20px 40px;
      text-align: center;
    }

    .footer p {
      font-size: 11px;
      color: rgba(255, 255, 255, 0.6);
      margin: 0;
    }
  </style>
</head>

<body>
  <table class="wrapper" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="480">
    <tr>
      <td class="header">
        <img class="logo" src="https://royalcanin.sociolive.in/assets/img/rc-logo.png" alt="Royal Canin" />
        <hr class="divider-line" />
      </td>
    </tr>
    <tr>
      <td class="body-section">
        <p>Hello,</p>
        <p>Use the OTP below to verify your email and create your password.</p>
        <div class="otp-box">
          <span class="otp-code">{{ $otp }}</span>
        </div>
        <p class="expiry-note">This OTP is valid for 10 minutes. If you did not request this, you can safely ignore this email.</p>
      </td>
    </tr>
    <tr>
      <td class="footer">
        <p>&copy; {{ date('Y') }} Royal Canin. All rights reserved.</p>
      </td>
    </tr>
  </table>
</body>

</html>
