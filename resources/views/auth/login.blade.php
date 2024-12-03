<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Form</title>
        <style>
            body {
                margin: 0;
                font-family: Arial, sans-serif;
                background-image: url(../images/login_bg.jpg);
                /* Add your background image here */
                background-size: cover;
                /* Cover the whole page */
                background-position: center;
                /* Center the background image */
                display: flex;
                align-items: center;
                /* Center vertically */
                justify-content: center;
                /* Center horizontally */
                height: 100vh;
                /* Full height */
            }

            #login-form-container {
                display: flex;
                background-color: rgba(255, 255, 255, 0.9);
                /* Semi-transparent background for the form */
                border-radius: 8px;
                /* Rounded corners */
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
                /* Shadow effect */
                max-width: 800px;
                /* Maximum width for the card */
                width: 100%;
                /* Full width up to the max */
            }

            .login-row {
                display: flex;
                width: 100%;
            }

            .login-image {
                flex: 1;
                background-color: #B0262E;
                color: white;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .login-image img {
                height: 150px;
                width: 150px;
                margin-bottom: 20px;
            }

            .login-form {
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding: 40px;
            }

            .login-form-header h3 {
                text-align: center;
                margin-bottom: 20px;
            }

            .form-group {
                margin-bottom: 15px;
            }

            .form-control {
                width: 100%;
                padding: 10px;
                border: 1px solid #ced4da;
                border-radius: 4px;
            }

            .btn-custom {
                background-color: #B0262E;
                color: white;
                border: none;
                padding: 10px;
                cursor: pointer;
                width: 100%;
            }

            .btn-custom:hover {
                background-color: #ef5252;
            }

            .btn-primary {
                background-color: #6c757d;
                color: white;
                border: none;
                padding: 10px;
                cursor: pointer;
                width: 100%;
            }

            .btn-primary:hover {
                background-color: #5a6268;
            }
        </style>
    </head>

    <body>

        <div id="login-form-container">
            <div class="login-row">
                <div class="login-image">
                    <img src="{{ asset('images/Universitas Ubudiyah Indonesia.png') }}" alt="University Logo">
                    {{-- <h3>Sistem Informasi Penjadwalan Kuliah Universitas Ubudiyah Indonesia</h3> --}}
                </div>
                <div class="login-form">
                    <div class="login-form-header">
                        <h3>Login</h3>
                    </div>
                    <div class="login-form-body">
                        <form method="POST" action="{{ URL::to('/login') }}">
                            {!! csrf_field() !!}
                            @include('errors.form_errors')

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" placeholder="Email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" class="form-control" placeholder="Password" name="password"
                                    required>
                            </div>

                            <div class="form-group">
                                <input type="submit" name="submit" value="SIGN IN" class="btn btn-custom">
                            </div>
                            <div class="form-group">
                                <input type="button" value="Register" class="btn btn-custom"
                                    onclick="window.location.href='/register';">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </body>

</html>