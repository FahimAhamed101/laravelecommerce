<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Log in</title>
  <link rel="shortcut icon" href="{{ url('public/assets/dist/img/icons/favicon.ico') }}">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ url('public/assets/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ url('public/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ url('public/assets/dist/css/adminlte.min.css') }}">
</head>

<style>
  .login-page {
    background-image: url("public/assets/dist/img/loginpage.jpg");
    background-position: center;
    background-size: cover;
  }
</style>

<body class="hold-transition login-page">
  <!-- <img src="{{ url('assets/images/logo.png') }}" alt="" width="115" height="55" style=""> -->
  <div class="login-box" style="width: 450px; margin-right: 550px;">
    <div class="login-logo">
      <a href="#"><b><b>e</b>Commerce.com</a></b>
    </div>

    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form action="#" method="post">

          {{ csrf_field() }}
          <div class="input-group mb-3">
            <input type="email" class="form-control" placeholder="Email" name="email" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Password" name="password" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-8">
              <div class="icheck-dark">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">
                  Remember Me
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" class="btn btn-dark btn-block">Sign In</button>
            </div>
            <!-- /.col -->
          </div>

          <p class="mb-1">
            <a href="#">I forgot my password</a>
          </p>

        </form>
      </div>
      <!-- /.login-card-body -->
      
    </div>
  </div>
  
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="{{ url('public/assets/plugins/jquery/jquery.min.js') }}"></script>
  <!-- Bootstrap 4 -->
  <script src="{{ url('public/assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <!-- AdminLTE App -->
  <script src="{{ url('public/assets/dist/js/adminlte.min.js') }}"></script>
</body>
</html>