<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GI Horizons Webinar</title>
    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/favicon.ico')}}" />
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom Fonts -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/font.css') }}" />

    <!-- External Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/login.min.css') }}" />
</head>

<body>
    <div class="container">
        <!--Section -->
        <div class="pt-3">
            <div class="row align-items-start g-5">
                <!-- Left: Logo & Text -->
                <div class="col-lg-6 col-12">
                    <img src="{{ asset('assets/img/logo-title.png') }}" alt="Royal Canin Logo" class="img-fluid gi-logo" />
                    <div class="content-wrapper mt-5">
                        <h3 class="main-heading">Evidence-based updates for everyday practice.</h3>

                        <p class="text-content">
                            Royal Canin proudly presents a six-part educational webinar series
                            dedicated to gastrointestinal health in small animals. This
                            initiative reflects our commitment to advancing veterinary science
                            and empowering clinicians with authoritative knowledge and
                            practical tools.
                        </p>

                        <p class="text-content mb-4">
                            We are honoured to feature Dr. K. G. Umesh as the distinguished
                            speaker for all six sessions. With decades of expertise in small
                            animal medicine and nutrition, Dr. Umesh will deliver
                            evidence-based insights, case discussions, and clinical strategies
                            to elevate veterinary practice.
                        </p>

                        <p class="text-content mb-0">
                            Explore the schedule below and register to secure your place.
                        </p>
                    </div>
                </div>

                <!-- Right: Cat Image -->
                <div class="col-lg-6 col-12 d-flex justify-content-center justify-content-lg-end">
                    <div class="cat-image-wrapper mt-3">
                        <img src="{{ asset('assets/img/1_Vet_Wordmark_Vet_Nutrition_Range_Cat_FR.png') }}"
                            alt="Veterinarian with cat"
                            class="cat-image" />
                    </div>
                </div>

            </div>
        </div>

        <!-- Content Section -->
        <div class="row gx-5 my-5">

            <!-- Left Column: Schedule Table -->
            <div class="col-lg-8 col-12 order-2 order-lg-1">
                <div class="table-wrapper">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Topic</th>
                                <th>Time (IST)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>27th May 2026</td>
                                <td>Wednesday</td>
                                <td>When Angry Pancreas throws a tantrum</td>
                                <td>1 PM-2 PM</td>
                            </tr>
                            <tr>
                                <td>26th June 2026</td>
                                <td>Friday</td>
                                <td>Hungry, hungry doggo- The EPI edition</td>
                                <td>7 PM-8 PM</td>
                            </tr>
                            <tr>
                                <td>22nd Jul-2026</td>
                                <td>Wednesday</td>
                                <td>Serial poopers - Loose stools, long tales</td>
                                <td>1 PM-2 PM</td>
                            </tr>
                            <tr>
                                <td>19th Aug 2026</td>
                                <td>Wednesday</td>
                                <td>Acute diarrhoea- New tricks , Same mess</td>
                                <td>1 PM-2 PM</td>
                            </tr>
                            <tr>
                                <td>23rd Sep-2026</td>
                                <td>Wednesday</td>
                                <td>Liver under pressure -Let liver Live. Part 1</td>
                                <td>1 PM-2 PM</td>
                            </tr>
                            <tr>
                                <td>21st Oct 2026</td>
                                <td>Wednesday</td>
                                <td>Liver under pressure -Let liver Live. Part 2</td>
                                <td>1 PM-2 PM</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: Buttons -->
            <div class="col-lg-4 col-12 d-flex flex-column align-items-center justify-content-start gap-3 mb-4 mb-md-0 mb-lg-0 order-1 order-lg-2">
                @guest
                <a href="{{ route('register') }}" class="btn btn-outline-custom">Register now</a>
                <a href="{{ route('login') }}" class="btn btn-primary-custom ">Click to Login</a>
                @else
                <a href="{{ route('webcast') }}" class="btn btn-primary-custom">View Webinars</a>
                @endguest
            </div>

        </div>
    </div>
</body>

</html>