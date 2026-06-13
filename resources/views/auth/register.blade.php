<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}" />
   <title>Register | Royal Canin</title>
   <!----------------- fonts ------------------------>
   <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/font.css') }}">
   <!----------------- stylesheets ------------------------>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/login.min.css') }}">
   <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

   <!-- Alpine.js -->
   <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
   <!-- Axios -->
   <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body>

   <main class="register-main" id="main" x-data="registerForm()" x-init="initAutocomplete">
      <div class="container">
         <div class="row">
            <div class="col-lg-6 sticky-sidebar">
               <div class="register-sidebar">
                  <div class="heading-box mt-lg-5 mt-md-5 mt-5">
                     <h2 class="text-white">GI HORIZONS</h2>
                     <p class="text-white">Join Royal Canin's exclusive six-part webinar series on gastrointestinal medicine, conducted by Dr. K. G. Umesh.</p>
                  </div>
                  <div class="dr-profile">
                     <div class="profile-img">
                        <img src="{{ asset('assets/img/magnific_Uymtp7Kwny.png') }}" class="img-fluid" alt="">
                     </div>
                     <div class="profile-content">
                        <h4 class="profile-name text-white">DR. KALLAHALLI UMESH,</h4>
                        <h5 class="profile-degree text-white">M.V.Sc., M.Sc</h5>
                     </div>
                  </div>
                  <div class="webinar-info">
                     <h4 class="text-white"><span>Duration</span><br>1 HOUR PER SESSION</h4>
                     <h4 class="text-white"><span>Dates</span><br>27<sup>TH</sup> MAY - 21<sup>ST</sup> OCT 2026</h4>
                     <h4 class="text-white"><span>Time</span><br>1 PM - 2 PM IST</h4>
                  </div>
                  <img src="{{ asset('assets/img/puppy-kitty.png') }}" class="img-fluid puppy-image" alt="">
               </div>
            </div>
            <div class="col-lg-6 col-md-12 col-12">

               <div class="register-wrapper">
                  <div class="d-flex justify-content-lg-end justify-content-md-center justify-content-center align-items-center my-lg-4 my-md-4 my-4">
                     <img src="{{ asset('assets/img/logo-title.png') }}" class="img-fluid" alt="GI Horizons Logo">
                  </div>

                  <h2 class="form-title mb-lg-0 mb-md-0 mb-4">Fill the Details</h2>
                  <h4 class="text-success mb-3">One-time registration ensures entry to all webinars.</h4>

                  <!-- Alert Messages -->
                  <div x-show="message.show" x-cloak>
                     <div :class="'alert alert-' + message.type" x-text="message.text"></div>
                  </div>

                  <form @submit.prevent="submitForm" id="registerForm" class="form">
                     @csrf
                     <div class="row">
                        <div class="col-lg-6 col-md-12 col-12 mb-3 mb-md-0 mb-lg-0">
                           <div class="input-group">
                              <span class="input-group-text">
                                 <select class="form-select" x-model="form.name_prefix">
                                    <option value="Dr.">Dr.</option>
                                    <option value="Mr.">Mr.</option>
                                    <option value="Mrs.">Mrs.</option>
                                    <option value="Ms.">Ms.</option>
                                 </select>
                              </span>
                              <input type="text" x-model="form.first_name" class="form-control" placeholder="First Name*">
                           </div>
                           <template x-if="errors.first_name">
                              <div class="text-danger small" x-text="errors.first_name[0]"></div>
                           </template>
                        </div>
                        <div class="col-lg-6 col-md-12 col-12">
                           <input type="text" x-model="form.last_name" class="form-control" placeholder="Last Name*">
                           <template x-if="errors.last_name">
                              <div class="text-danger small" x-text="errors.last_name[0]"></div>
                           </template>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-6 mt-3">
                           <input type="text" x-model="form.registration_number" class="form-control" placeholder="VCI Registration Number">
                        </div>
                        <div class="col-md-6 mt-3">
                           <input type="text" x-model="form.mobile_number" maxlength="10" class="form-control" placeholder="WhatsApp Number *">
                           <template x-if="errors.mobile_number">
                              <div class="text-danger small" x-text="errors.mobile_number[0]"></div>
                           </template>
                        </div>

                        <div class="col-md-6 mt-3">
                           <input type="email" x-model="form.email" class="form-control" placeholder="Email Address *">
                           <template x-if="errors.email">
                              <div class="text-danger small" x-text="errors.email[0]"></div>
                           </template>
                        </div>

                        <div class="col-md-6 mt-3">
                           <input type="text" x-model="form.clinic_name" class="form-control" placeholder="Pet Clinic / Hospital Name *">
                           <template x-if="errors.clinic_name">
                              <div class="text-danger small" x-text="errors.clinic_name[0]"></div>
                           </template>
                        </div>

                        <div class="col-md-6 city-dropdown mt-3">
                           <input type="text" class="form-control" id="citySelect" x-model="form.city" placeholder="City/Town *">
                           <template x-if="errors.city">
                              <div class="text-danger small" x-text="errors.city[0]"></div>
                           </template>
                        </div>
                        <div class="col-md-6 mt-3">
                           <input type="text" x-model="form.state" class="form-control" placeholder="State *">
                           <template x-if="errors.state">
                              <div class="text-danger small" x-text="errors.state[0]"></div>
                           </template>
                        </div>
                     </div>

                     <div class="form-check mt-3">
                        <input class="form-check-input custom-check" type="checkbox" x-model="form.terms" id="termsCheck">
                        <label class="form-check-label" for="termsCheck">
                           Please see our <a href="https://www.mars.com/privacy" target="_blank">Privacy Statement</a> to find out how Royal Canin collect and use your data.
                        </label>
                     </div>
                     <template x-if="errors.terms">
                        <div class="text-danger small" x-text="errors.terms[0]"></div>
                     </template>

                     <div class="form-check-label mt-3">
                        <p>I am over 18 and consent to the processing of my data for:</p>
                     </div>
                     <div class="form-check">
                        <input class="form-check-input custom-check" type="checkbox" x-model="form.sale_consent" id="saleConsent">
                        <label class="form-check-label" for="saleConsent">sale and purchase of products and services</label>
                     </div>
                     <div class="form-check">
                        <input class="form-check-input custom-check" type="checkbox" x-model="form.research_consent" id="researchConsent">
                        <label class="form-check-label" for="researchConsent">research to enhance product and service offerings.</label>
                     </div>

                     <div class="mt-3">
                        <button type="submit" class="btn btn-site" :disabled="loading" x-text="loading ? 'Processing...' : 'Submit'"></button>
                     </div>
                  </form>
                  <div class="mt-4">
                     <hr class="hr">
                     <p class="text-center redirect-link">If already registered, <a href="{{ route('login') }}">click here</a></p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </main>

   <!--------------- javascript  ---------------->
   <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
   <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

   <script>
      function registerForm() {
         return {
            form: {
               name_prefix: 'Dr.',
               first_name: '',
               last_name: '',
               registration_number: '',
               mobile_number: '',
               email: '',
               clinic_name: '',
               city: '',
               state: '',
               terms: false,
               sale_consent: true,
               research_consent: true,
            },
            errors: {},
            loading: false,
            message: {
               show: false,
               type: 'success',
               text: ''
            },

            submitForm() {
               this.loading = true;
               this.errors = {};
               this.message.show = false;

               axios.post('{{ route("register") }}', this.form)
                  .then(response => {
                     if (response.data.success) {
                        this.message.type = 'success';
                        this.message.text = response.data.message;
                        this.message.show = true;

                        setTimeout(() => {
                           window.location.href = response.data.redirect_url;
                        }, 1000);
                     }
                  })
                  .catch(error => {
                     if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors;
                        this.message.type = 'danger';
                        this.message.text = 'Please fix the errors below.';
                     } else if (error.response && error.response.data.message) {
                        this.message.type = 'danger';
                        this.message.text = error.response.data.message;
                     } else {
                        this.message.type = 'danger';
                        this.message.text = 'An error occurred. Please try again.';
                     }
                     this.message.show = true;
                     window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                     });
                  })
                  .finally(() => {
                     this.loading = false;
                  });
            },

            initAutocomplete() {
               $("#citySelect").autocomplete({
                  source: "{{ route('fetch.cities') }}",
                  minLength: 2,
                  select: (event, ui) => {
                     this.form.city = ui.item.value;
                     this.form.state = ui.item.state;
                  }
               });
            }
         }
      }
   </script>

   <style>
      [x-cloak] {
         display: none !important;
      }
   </style>
</body>

</html>