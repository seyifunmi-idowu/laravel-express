<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{% static 'assets/img/apple-icon.png' %}">
    <link rel="icon" type="image/png" href="{% static 'assets/img/favicon.png' %}">
    <title>
        Fele Express - Business
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet"/>
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/soft-ui-dashboard.css') }}?v=1.0.7" rel="stylesheet">

    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

</head>

<body class="">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg position-absolute top-0 z-index-3 w-100 shadow-none my-3 navbar-transparent mt-4">
    <div class="container">
        <a class="navbar-brand m-0" href="/">
            <img src="{{ asset('assets/img/fele-logo.png') }}" class="navbar-brand-img h-100" alt="main_logo">
        </a>
    </div>
  </nav>
  <!-- End Navbar -->
  <main class="main-content  mt-0">
    <section class="min-vh-100 mb-8">
      <div class="page-header align-items-start min-vh-50 pt-5 pb-11 m-3 border-radius-lg" style="background-image: url('{{ asset('assets/img/background.jpg') }}');">
        <span class="mask bg-gradient-dark opacity-6"></span>
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-5 text-center mx-auto">
              <h1 class="text-white mb-2 mt-5">Welcome back!</h1>
<!--              <p class="text-lead text-white">Use these awesome forms to login or create new account in your project for free.</p>-->
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row mt-lg-n10 mt-md-n11 mt-n10">
          <div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
            <div class="card z-index-0">
              <div class="card-header text-center pt-4">
                <h5>Login</h5>
              </div>
              <div class="card-body">
                <form method="POST" action="{{ route('business-login') }}" role="form text-left">
                    @csrf
                    <div class="mb-3">
                        <input name="email" type="email" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="email-addon">
                        @if ($errors->has('email'))
                            @foreach ($errors->get('email') as $error)
                                <span class="text-danger error-message">{{ $error }}</span>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <input name="password" type="password" class="form-control" placeholder="Password" aria-label="Password" aria-describedby="password-addon">
                        @if ($errors->has('password'))
                            @foreach ($errors->get('password') as $error)
                                <span class="text-danger error-message">{{ $error }}</span>
                            @endforeach
                        @endif
                    </div>
                    @if (session('messages'))
                        @foreach (session('messages') as $message)
                            <span class="text-danger error-message">{{ $message }}</span>
                        @endforeach
                    @endif
                    <div class="text-center">
                        <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Login</button>
                    </div>
                    <p class="text-sm mt-3 mb-0"> Don't have an account? <a href="{{ route('business-register') }}" class="text-dark font-weight-bolder">Register</a></p>
                </form>
            </div>
          </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</body>

</html>
