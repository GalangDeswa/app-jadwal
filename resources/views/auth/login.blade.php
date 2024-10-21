<!DOCTYPE html>
<html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Styles -->
        @include('partials.styles')
        @yield('styles')

        <title>Login | Sistem Informasi Penjadwalan Kuliah Universitas Ubudiyah Indonesia</title>
    </head>

    <body class="login-page">
        <div class="container">
            <div class="row">
                <div
                    class="col-xs-12 col-md-4 col-sm-8 col-lg-4 col-md-offset-4 col-sm-offset-2 col-lg-offset-4 d-flex justify-content-center">

                    <div id="login-form-container">
                        {{-- <div class="row">
                            <div class="col-sm">
                                <div class="text-center">
                                    <img src="{{ asset('images/Universitas Ubudiyah Indonesia.png') }}"
                                        style="height: 150px;width: 150px;" alt="...">
                                </div>
                            </div>
                            <div class="col-sm">
                                <h3 class="text-center" style="color: white">Sistem Informasi Penjadwalan Kuliah
                                    Universitas Ubudiyah
                                    Indonesia
                                </h3>
                            </div>
                        </div> --}}

                        <div class="login-form-header">

                            <h3 class="text-center">Login
                            </h3>
                        </div>

                        <div class="login-form-body">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <form method="POST" action="{{ URL::to('/login') }}">
                                        {!! csrf_field() !!}
                                        @include('errors.form_errors')

                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" placeholder="Email" name="email">
                                        </div>

                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" class="form-control" placeholder="Password"
                                                name="password">
                                        </div>

                                        <div class="form-group">
                                            <input type="submit" name="submit" value="SIGN IN"
                                                class="btn btn-lg btn-block btn-custom">
                                        </div>

                                        <div class="form-group">
                                            <a href="/request_reset" class="btn btn-lg btn-block btn-primary">Lupa
                                                password?</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Scripts -->
        @include('partials.scripts')
        @yield('scripts')
    </body>

</html>