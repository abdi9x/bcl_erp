<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{config('app.name')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/icon.png') }}">

    <!-- App css -->
    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('plugins/animate/animate.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('plugins/jquery-toast/dist/jquery.toast.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body class="account-body accountbg">

    <!-- Log In page -->
    <div class="container">
        <div class="row vh-100 d-flex justify-content-center">
            <div class="col-12 align-self-center">
                <div class="row">
                    <div class="col-lg-5 mx-auto">
                        <div class="card bounceIn animated">
                            <div class="card-body p-0 auth-header-box">
                                <div class="text-center p-3">
                                    <a href="index.html" class="logo logo-admin">
                                        <img src="{{ URL::asset('assets/images/logo.png') }}" height="50" alt="logo" class="auth-logo">
                                    </a>
                                    <h4 class="mt-3 mb-1 font-weight-semibold text-white font-18">{{config('app.tagline')}}</h4>
                                    <p class="text-muted  mb-0">Sign in to continue</p>
                                </div>
                            </div>
                            <div class="card-body p-0">

                                <!-- Tab panes -->
                                <form class="form-horizontal auth-form" action="{{ route('login') }}" method="POST">
                                    <div class="pt-2 pl-3 pr-3">
                                        @csrf
                                        <div class="form-group mb-2">
                                            <label for="email">Email</label>
                                            <div class="input-group">
                                                <input type="email" class="form-control" name="email" id="email" placeholder="Email.." value="{{ old('email') }}" required>
                                            </div>
                                        </div><!--end form-group-->

                                        <div class="form-group mb-2">
                                            <label for="userpassword">Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="password" id="password" required placeholder="Enter password">
                                            </div>
                                        </div><!--end form-group-->

                                        <div class="form-group row my-3">
                                            <div class="col-sm-6">
                                                <div class="custom-control custom-switch switch-success">
                                                    <input type="checkbox" class="custom-control-input" name="remember" id="customSwitchSuccess">
                                                    <label class="custom-control-label text-muted" for="customSwitchSuccess">Remember me</label>
                                                </div>
                                            </div><!--end col-->
                                        </div><!--end form-group-->

                                        <div class="form-group mb-0 row">
                                            <div class="col-12">
                                                <button class="btn btn-primary btn-block waves-effect waves-light" type="submit">Log In <i class="fas fa-sign-in-alt ml-1"></i></button>
                                            </div><!--end col-->
                                        </div> <!--end form-group-->
                                    </div>
                                </form><!--end form-->

                            </div><!--end card-body-->
                            <div class="card-body bg-light-alt text-center">
                                <span class="text-muted d-none d-sm-inline-block"><a href="http://alphaproject.rf.gd/#about-me" target="_blank">Alphastudio</a> © <?= date('Y') ?></span>
                            </div>
                        </div><!--end card-->
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end col-->
        </div><!--end row-->
    </div><!--end container-->
    <!-- End Log In page -->

    <!-- jQuery  -->
    <script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/waves.js') }}"></script>
    <script src="{{ URL::asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/simplebar.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/jquery-toast/dist/jquery.toast.min.js') }}"></script>
    <script>
        @if($error = Session::get('error'))
        $.toast({
            text: "{{ $error }}",
            heading: 'Result',
            position: 'top-center',
            hideAfter: 5000,
            icon: 'error',
        });
        @endif
    </script>
</body>

</html>