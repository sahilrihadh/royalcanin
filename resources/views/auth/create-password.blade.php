<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}" />
   <title>Create Password | Royal Canin</title>

   <!----------------- fonts ------------------------>
   <link rel="stylesheet" type="text/css" href="{{ asset('fonts/font.css') }}">

   <!----------------- stylesheets ------------------------>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/login.min.css') }}">

   <!-- Alpine.js & Axios -->
   <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

   <style>
      [x-cloak] {
         display: none !important;
      }

      .otp-hint {
         font-size: 0.8rem;
         color: #6c757d;
      }

      .password-rules {
         font-size: 0.78rem;
         color: #6c757d;
         padding-left: 1.1rem;
         margin-bottom: 0;
      }

      .password-rules li.valid {
         color: #198754;
      }

      .resend-link {
         font-size: 0.85rem;
      }

      .resend-link.disabled {
         pointer-events: none;
         color: #adb5bd !important;
      }
   </style>
</head>

<body>

   <!-- Header with Royal Canin Logo -->
   <header class="header py-4">
      <div class="container">
         <div class="row justify-content-end">
            <div class="col-auto">
               <img src="{{ asset('assets/img/rc-logo.png') }}" class="img-fluid royal-canin-logo" alt="Royal Canin Logo">
            </div>
         </div>
      </div>
   </header>

   <main class="login-main" x-data="createPasswordForm()" x-init="init()" x-cloak>
      <div class="container-fluid">
         <div class="row g-0 min-vh-75 align-items-center">

            <div class="col-lg-4 col-md-7 col-12 order-lg-2 order-md-2 order-1">
               <div class="login-wrapper">

                  <div class="login-header mb-4">
                     <div class="d-flex align-items-center justify-content-center">
                        <div class="gi-logo-wrapper me-3">
                           <img src="{{ asset('assets/img/logo-title.png') }}" class="img-fluid gi-logo" alt="GI Horizons Logo">
                        </div>
                     </div>
                  </div>

                  <div class="login-box">
                     <h2 class="form-title mb-4" x-text="step === 4 ? 'SUCCESS' : 'CREATE PASSWORD'"></h2>

                     <!-- Alert Message -->
                     <div x-show="message.show" x-cloak class="mb-3">
                        <div :class="'alert alert-' + message.type" x-text="message.text"></div>
                     </div>

                     <!-- STEP 1: Enter Email -->
                     <form x-show="step === 1" @submit.prevent="sendOtp" class="form">
                        @csrf
                        <div class="mb-4">
                           <label class="form-label">Email Address</label>
                           <input type="email" x-model="form.email_id" class="form-control custom-input" placeholder="Email Address" required>
                           <template x-if="errors.email_id">
                              <div class="text-danger small mt-1" x-text="errors.email_id[0]"></div>
                           </template>
                        </div>
                        <div class="mt-4">
                           <button type="submit" class="btn btn-site w-100" :disabled="loading" x-text="loading ? 'Sending OTP...' : 'Send OTP'"></button>
                        </div>
                     </form>

                     <!-- STEP 2: Enter OTP -->
                     <form x-show="step === 2" @submit.prevent="verifyOtp" class="form">
                        @csrf
                        <p class="otp-hint mb-3">We've sent a 6-digit OTP to <strong x-text="form.email_id"></strong>.</p>
                        <div class="mb-3">
                           <label class="form-label">Enter OTP</label>
                           <input type="text" inputmode="numeric" maxlength="6" x-model="form.otp" class="form-control custom-input" placeholder="6-digit OTP" required>
                           <template x-if="errors.otp">
                              <div class="text-danger small mt-1" x-text="errors.otp[0]"></div>
                           </template>
                        </div>
                        <div class="mb-4">
                           <a href="#" class="resend-link" :class="{ disabled: resendCooldown > 0 }" @click.prevent="sendOtp(true)">
                              <span x-show="resendCooldown === 0">Resend OTP</span>
                              <span x-show="resendCooldown > 0">Resend OTP in <span x-text="resendCooldown"></span>s</span>
                           </a>
                        </div>
                        <div class="mt-4">
                           <button type="submit" class="btn btn-site w-100" :disabled="loading" x-text="loading ? 'Verifying...' : 'Verify OTP'"></button>
                        </div>
                     </form>

                     <!-- STEP 3: Set New Password -->
                     <form x-show="step === 3" @submit.prevent="setPassword" class="form">
                        @csrf
                        <div class="mb-3">
                           <label class="form-label">New Password</label>
                           <input type="password" x-model="form.password" class="form-control custom-input" placeholder="New Password" required>
                           <template x-if="errors.password">
                              <div class="text-danger small mt-1" x-text="errors.password[0]"></div>
                           </template>
                        </div>
                        <div class="mb-3">
                           <label class="form-label">Confirm Password</label>
                           <input type="password" x-model="form.password_confirmation" class="form-control custom-input" placeholder="Confirm Password" required>
                        </div>
                        <ul class="password-rules mb-4">
                           <li :class="{ valid: rules.length }">At least 8 characters</li>
                           <li :class="{ valid: rules.upper }">One uppercase letter</li>
                           <li :class="{ valid: rules.lower }">One lowercase letter</li>
                           <li :class="{ valid: rules.number }">One number</li>
                           <li :class="{ valid: rules.special }">One special character</li>
                           <li :class="{ valid: rules.match }">Passwords match</li>
                        </ul>
                        <div class="mt-4">
                           <button type="submit" class="btn btn-site w-100" :disabled="loading || !allRulesValid" x-text="loading ? 'Saving...' : 'Create Password'"></button>
                        </div>
                     </form>

                     <!-- STEP 4: Success -->
                     <div x-show="step === 4" class="text-center py-3">
                        <p class="mb-1">Password created successfully!</p>
                        <p class="otp-hint">Redirecting you to the login page...</p>
                     </div>
                  </div>

                  <div class="text-center mt-4">
                     <p class="redirect-link mb-0">Remembered your password? <a href="{{ route('login') }}">Login here</a></p>
                  </div>

               </div>
            </div>

         </div>
      </div>
   </main>

   <script>
      function createPasswordForm() {
         return {
            step: 1,
            form: {
               email_id: '{{ $email }}',
               otp: '',
               password: '',
               password_confirmation: '',
            },
            errors: {},
            loading: false,
            resendCooldown: 0,
            resendTimer: null,
            message: {
               show: false,
               type: 'success',
               text: ''
            },

            init() {
               axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
               axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            },

            get rules() {
               const p = this.form.password;
               return {
                  length: p.length >= 8,
                  upper: /[A-Z]/.test(p),
                  lower: /[a-z]/.test(p),
                  number: /[0-9]/.test(p),
                  special: /[^A-Za-z0-9]/.test(p),
                  match: p.length > 0 && p === this.form.password_confirmation,
               };
            },

            get allRulesValid() {
               const r = this.rules;
               return r.length && r.upper && r.lower && r.number && r.special && r.match;
            },

            resetAlerts() {
               this.errors = {};
               this.message.show = false;
            },

            handleError(error, fallback) {
               let errorMsg = fallback;
               if (error.response) {
                  if (error.response.status === 422 && error.response.data.errors) {
                     this.errors = error.response.data.errors;
                     errorMsg = error.response.data.message || 'Please fix the errors below.';
                  } else if (error.response.data && error.response.data.message) {
                     errorMsg = error.response.data.message;
                  }
               } else if (error.request) {
                  errorMsg = 'No response from server. Please check your connection.';
               }
               this.message.type = 'danger';
               this.message.text = errorMsg;
               this.message.show = true;
            },

            startResendCooldown() {
               this.resendCooldown = 30;
               clearInterval(this.resendTimer);
               this.resendTimer = setInterval(() => {
                  this.resendCooldown--;
                  if (this.resendCooldown <= 0) {
                     clearInterval(this.resendTimer);
                  }
               }, 1000);
            },

            sendOtp(isResend = false) {
               this.resetAlerts();

               if (!this.form.email_id) {
                  this.message.type = 'danger';
                  this.message.text = 'Please enter your email address.';
                  this.message.show = true;
                  return;
               }

               this.loading = true;

               axios.post('{{ route("password.sendOtp") }}', {
                  email_id: this.form.email_id,
               })
               .then(response => {
                  this.message.type = 'success';
                  this.message.text = response.data.message;
                  this.message.show = true;
                  this.step = 2;
                  this.startResendCooldown();
               })
               .catch(error => this.handleError(error, 'Could not send OTP. Please try again.'))
               .finally(() => this.loading = false);
            },

            verifyOtp() {
               this.resetAlerts();
               this.loading = true;

               axios.post('{{ route("password.verifyOtp") }}', {
                  email_id: this.form.email_id,
                  otp: this.form.otp,
               })
               .then(response => {
                  this.message.type = 'success';
                  this.message.text = response.data.message;
                  this.message.show = true;
                  this.step = 3;
               })
               .catch(error => this.handleError(error, 'Could not verify OTP. Please try again.'))
               .finally(() => this.loading = false);
            },

            setPassword() {
               this.resetAlerts();

               if (!this.allRulesValid) {
                  this.message.type = 'danger';
                  this.message.text = 'Please make sure your password meets all the requirements.';
                  this.message.show = true;
                  return;
               }

               this.loading = true;

               axios.post('{{ route("password.set") }}', {
                  email_id: this.form.email_id,
                  password: this.form.password,
                  password_confirmation: this.form.password_confirmation,
               })
               .then(response => {
                  this.step = 4;
                  setTimeout(() => {
                     window.location.href = response.data.redirect_url;
                  }, 1800);
               })
               .catch(error => this.handleError(error, 'Could not create password. Please try again.'))
               .finally(() => this.loading = false);
            },
         }
      }
   </script>

</body>

</html>
