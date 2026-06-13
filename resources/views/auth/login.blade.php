<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}" />
   <title>Login | Royal Canin</title>

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

   <!-- Main Login Section with Alpine.js -->
   <main class="login-main" x-data="loginForm()" x-cloak>
      <div class="container-fluid">
         <div class="row g-0 min-vh-75 align-items-center">

            <!-- CENTER COLUMN: Login Box -->
            <div class="col-lg-4 col-md-7 col-12 order-lg-2 order-md-2 order-1">
               <div class="login-wrapper">

                  <!-- Top Logo Section -->
                  <div class="login-header mb-4">
                     <div class="d-flex align-items-center justify-content-center">
                        <div class="gi-logo-wrapper me-3">
                           <img src="{{ asset('assets/img/logo-title.png') }}" class="img-fluid gi-logo" alt="GI Horizons Logo">
                        </div>
                     </div>
                  </div>

                  <!-- Login Form Container -->
                  <div class="login-box">
                     <h2 class="form-title mb-4">LOGIN</h2>

                     <!-- Alert Message -->
                     <div x-show="message.show" x-cloak class="mb-3">
                        <div :class="'alert alert-' + message.type" x-text="message.text"></div>
                     </div>

                     <!-- Login Form -->
                     <form @submit.prevent="submitForm" id="login-form" class="form">
                        @csrf

                        <div class="mb-4">
                           <label class="form-label">Email Address</label>
                           <input type="email" x-model="form.email_id" class="form-control custom-input" placeholder="Email Address" required>
                           <template x-if="errors.email_id">
                              <div class="text-danger small mt-1" x-text="errors.email_id[0]"></div>
                           </template>
                        </div>

                        <div class="mt-4">
                           <button type="submit" class="btn btn-site w-100" :disabled="loading" x-text="loading ? 'Processing...' : 'Submit'"></button>
                        </div>
                     </form>
                  </div>

                  <!-- Registration Link -->
                  <div class="text-center mt-4">
                     <p class="redirect-link mb-0">If not registered yet, <a href="{{ route('register') }}">click here</a></p>
                  </div>

               </div>
            </div>

         </div>
      </div>
   </main>

   <script>
      function loginForm() {
         return {
            form: {
               email_id: '',
            },
            errors: {},
            loading: false,
            message: {
               show: false,
               type: 'success',
               text: ''
            },

            submitForm() {
               // Reset previous errors and messages
               this.loading = true;
               this.errors = {};
               this.message.show = false;

               // Basic client-side validation
               if (!this.form.email_id) {
                  this.message.type = 'danger';
                  this.message.text = 'Please enter your email address.';
                  this.message.show = true;
                  this.loading = false;
                  return;
               }

               if (!this.isValidEmail(this.form.email_id)) {
                  this.message.type = 'danger';
                  this.message.text = 'Please enter a valid email address.';
                  this.message.show = true;
                  this.loading = false;
                  return;
               }

               // Set CSRF token for Axios
               axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
               axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

               // Make API request
               axios.post('{{ route("login.process") }}', this.form)
                  .then(response => {
                     if (response.data.success) {
                        this.message.type = 'success';
                        this.message.text = response.data.message + ' Redirecting...';
                        this.message.show = true;

                        // Redirect after delay
                        setTimeout(() => {
                           window.location.href = response.data.redirect_url;
                        }, 1000);
                     } else {
                        this.message.type = 'danger';
                        this.message.text = response.data.message;
                        this.message.show = true;
                     }
                  })
                  .catch(error => {
                     let errorMsg = 'An error occurred. Please try again.';

                     if (error.response) {
                        // Handle validation errors
                        if (error.response.status === 422 && error.response.data.errors) {
                           this.errors = error.response.data.errors;
                           errorMsg = 'Please fix the errors below.';
                        }
                        // Handle authentication errors
                        else if (error.response.status === 401) {
                           errorMsg = error.response.data.message || 'Invalid email address. Please register first.';
                        }
                        // Handle other errors
                        else if (error.response.data && error.response.data.message) {
                           errorMsg = error.response.data.message;
                        }
                     } else if (error.request) {
                        errorMsg = 'No response from server. Please check your connection.';
                     } else {
                        errorMsg = error.message;
                     }

                     this.message.type = 'danger';
                     this.message.text = errorMsg;
                     this.message.show = true;
                  })
                  .finally(() => {
                     this.loading = false;
                  });
            },

            isValidEmail(email) {
               const emailRegex = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/;
               return emailRegex.test(email);
            }
         }
      }
   </script>

</body>

</html>