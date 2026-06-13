<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Royal Canin Admin Login</title>

  <!-------------- Fonts ------------------>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="{{ asset('assets/css/admin-custom.css') }}" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
  <main>
    <section class="vh-100 gradient-custom">
      <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card bg-dark text-white" style="border-radius: 1rem;">
              <div class="card-body p-5 text-center">
                <div class="mb-md-4 mt-md-4">
                  <h2 class="fw-bold mb-4 text-uppercase">ADMIN LOGIN</h2>

                  @if($errors->any())
                  <div class="alert alert-danger">
                    {{ $errors->first() }}
                  </div>
                  @endif

                  <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf
                    <div class="form-outline form-white mb-4">
                      <label class="form-label" for="username">Username</label>
                      <input type="text" name="username" id="username" class="form-control form-control-lg" value="{{ old('username') }}" required />
                    </div>
                    <div class="form-outline form-white mb-4">
                      <label class="form-label" for="password">Password</label>
                      <input type="password" name="password" id="password" class="form-control form-control-lg" required />
                    </div>
                    <button class="btn btn-outline-light btn-lg px-5" type="submit">Login</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>