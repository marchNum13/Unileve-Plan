<?php  
  $titlePage = "Sign In"
?>

<!doctype html>
<html lang="en" class="light-theme">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="assets/css/bootstrap-extended.css" rel="stylesheet" />
  <link href="assets/css/style.css" rel="stylesheet" />
  <link href="assets/css/icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

  <!-- loader-->
  <link href="assets/css/pace.min.css" rel="stylesheet" />
  
  <?php include "partial/seo.php" ?>
</head>

<body>

  <!--start wrapper-->
  <div class="wrapper">

    <!--start content-->
    <main class="authentication-content">
      <div class="container-fluid">
        <div class="authentication-card">
          <div class="card shadow rounded-0 overflow-hidden">
            <div class="row g-0">
              <div class="col-lg-6 bg-login d-flex align-items-center justify-content-center">
                <img src="assets/images/error/login-img.jpg" class="img-fluid" alt="">
              </div>
              <div class="col-lg-6">
                <div class="card-body p-4 p-sm-5">
                  <h4 class="card-title">Sign In</h4>
                  <p class="card-text mb-5">Bergabung dan mulai bangun jaringan Anda sekarang juga!</p>
                  <form class="form-body">
                    <div class="row g-3">
                      <!-- email -->
                      <div class="col-12">
                        <label for="inputEmailAddress" class="form-label">Username / Email Address</label>
                        <div class="ms-auto position-relative">
                          <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                              class="bi bi-envelope-fill"></i></div>
                          <input type="text" class="form-control radius-30 ps-5" id="inputEmailAddress"
                            placeholder="Username / Email Address">
                        </div>
                      </div>
                      <!-- pass -->
                      <div class="col-12">
                        <label for="inputChoosePassword" class="form-label">Enter Password</label>
                        <div class="ms-auto position-relative">
                          <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                              class="bi bi-lock-fill"></i></div>
                          <input type="password" class="form-control radius-30 ps-5" id="inputChoosePassword"
                            placeholder="Enter Password">
                        </div>
                      </div>
                      <!-- button -->
                      <div class="col-12">
                        <div class="d-grid">
                          <button id="btnSubmit" type="submit" name="submit" class="btn btn-primary radius-30">Sign In</button>
                          <button id="btnLoading" class="btn btn-primary radius-30" type="button" disabled style="display: none;"> <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                          </button>

                          <!-- Script JavaScript -->
                          <script>
                            document.getElementById('btnSubmit').addEventListener('click', function(e) {
                              // Sembunyikan tombol Sign Up
                              this.style.display = 'none';
                              // Tampilkan tombol Loading
                              document.getElementById('btnLoading').style.display = 'inline-block';
                            });
                          </script>
                        </div>
                      </div>
                      <!-- link login -->
                      <div class="col-12">
                        <p class="mb-0">Belum punya akun? <a href="signup">Sign Up disini!</a>
                        </p>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!--end page main-->

  </div>
  <!--end wrapper-->


  <!--plugins-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/pace.min.js"></script>


</body>

</html>