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
   <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

   <!-- Alpine.js & Axios -->
   <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
   <!-- jQuery for autocomplete -->
   <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
   <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

   <style>
      [x-cloak] {
         display: none !important;
      }
      
      .ui-autocomplete {
         max-height: 200px;
         overflow-y: auto;
         overflow-x: hidden;
         z-index: 1000;
         background: white;
         border: 1px solid #ddd;
         border-radius: 4px;
         box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      }
      
      .ui-menu-item {
         padding: 8px 12px;
         cursor: pointer;
      }
      
      .ui-menu-item:hover {
         background-color: #f0f0f0;
      }
      
      .ui-state-focus {
         background-color: #e0e0e0 !important;
         border: none !important;
      }
      
      .city-hint {
         font-size: 0.75rem;
         color: #6c757d;
         margin-top: 0.25rem;
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
   <main class="login-main" x-data="loginForm()" x-init="initAutocomplete()" x-cloak>
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

                        <div class="mb-2">
                           <label class="form-label">Password</label>
                           <input type="password" x-model="form.password" class="form-control custom-input" placeholder="Password" required>
                           <template x-if="errors.password">
                              <div class="text-danger small mt-1" x-text="errors.password[0]"></div>
                           </template>
                        </div>

                        <div class="mb-4 text-end">
                           <a href="{{ route('password.create') }}" class="small">Create/Forgot your password?</a>
                        </div>

                        <!-- City Input with Autocomplete -->
                        <div class="mb-4">
                           <label class="form-label">City/Town</label>
                           <div class="city-input-wrapper">
                              <input type="text" 
                                     id="citySelect" 
                                     x-model="form.city" 
                                     class="form-control custom-input" 
                                     placeholder="Search or type city name"
                                     @input="onCityInput()">
                              <div class="city-hint" x-show="!citySelected && form.city">
                                 <i class="bi bi-info-circle"></i> Type to search from database or enter manually
                              </div>
                              <div class="city-hint text-success" x-show="citySelected">
                                 <i class="bi bi-check-circle"></i> City found in database
                              </div>
                           </div>
                           <template x-if="errors.city">
                              <div class="text-danger small mt-1" x-text="errors.city[0]"></div>
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
         password: '',
         city: '',
      },
      errors: {},
      loading: false,
      message: {
         show: false,
         type: 'success',
         text: ''
      },
      citySelected: false,

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

         if (!this.form.password) {
            this.message.type = 'danger';
            this.message.text = 'Please enter your password.';
            this.message.show = true;
            this.loading = false;
            return;
         }

         // Validate city
         if (!this.form.city) {
            this.message.type = 'danger';
            this.message.text = 'Please enter your city/town.';
            this.message.show = true;
            this.loading = false;
            return;
         }

         // Set CSRF token for Axios
         axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
         axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

         // Log the data being sent
         console.log('Sending data:', {
            email_id: this.form.email_id,
            city: this.form.city
         });

         // Make API request with city data
         axios.post('{{ route("login") }}', {
            email_id: this.form.email_id,
            password: this.form.password,
            city: this.form.city
         })
         .then(response => {
            console.log('Response:', response.data);
            if (response.data.success) {
               this.message.type = 'success';
               this.message.text = response.data.message + ' Redirecting...';
               this.message.show = true;

               setTimeout(() => {
                  window.location.href = response.data.redirect_url;
               }, 1000);
            } else if (response.data.needs_password_setup) {
               this.message.type = 'warning';
               this.message.text = response.data.message + ' Redirecting...';
               this.message.show = true;

               setTimeout(() => {
                  window.location.href = response.data.redirect_url;
               }, 1200);
            } else {
               this.message.type = 'danger';
               this.message.text = response.data.message;
               this.message.show = true;
            }
         })
         .catch(error => {
            console.error('Error:', error);
            let errorMsg = 'An error occurred. Please try again.';

            if (error.response) {
               console.log('Error response:', error.response.data);
               if (error.response.status === 422 && error.response.data.errors) {
                  this.errors = error.response.data.errors;
                  errorMsg = 'Please fix the errors below.';
               } else if (error.response.status === 401) {
                  errorMsg = error.response.data.message || 'Invalid email address. Please register first.';
               } else if (error.response.data && error.response.data.message) {
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
      },

      onCityInput() {
         this.citySelected = false;
      },

      initAutocomplete() {
         const self = this;
         
         $("#citySelect").autocomplete({
            source: function(request, response) {
               if (request.term.length < 2) {
                  response([]);
                  return;
               }
               
               $.ajax({
                  url: "{{ route('fetch.cities') }}",
                  dataType: "json",
                  data: {
                     term: request.term
                  },
                  success: function(data) {
                     if (data.length === 0) {
                        response([]);
                        return;
                     }
                     
                     response($.map(data, function(item) {
                        return {
                           label: item.city_name + (item.state_name ? ', ' + item.state_name : ''),
                           value: item.city_name
                        };
                     }));
                  },
                  error: function() {
                     response([]);
                  }
               });
            },
            minLength: 2,
            select: function(event, ui) {
               self.form.city = ui.item.value;
               self.citySelected = true;
               $(this).val(ui.item.value);
               return false;
            },
            change: function(event, ui) {
               if (!ui.item) {
                  const typedValue = $(this).val();
                  if (typedValue) {
                     self.form.city = typedValue;
                     self.citySelected = false;
                  } else {
                     self.form.city = '';
                     self.citySelected = false;
                  }
               }
            }
         });
      }
   }
}
   </script>

</body>

</html>