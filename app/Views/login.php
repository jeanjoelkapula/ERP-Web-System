<?php 
$request = \Config\Services::request();
echo view('_general/header');?>
<link rel="stylesheet" href="/assets/vendor/css/pages/authentication.css">
<!-- Content -->

<div class="authentication-wrapper authentication-3">
    <div class="authentication-inner">

        <!-- Side container -->
        <!-- Do not display the container on extra small, small and medium screens -->
        <div class="d-none d-lg-flex col-lg-8 align-items-center ui-bg-cover ui-bg-overlay-container p-5" style="background-image: url('/assets/img/bg/1.jpg');">
            <div class="ui-bg-overlay bg-dark opacity-50"></div>

            <!-- Text -->
            <div class="w-100 text-white px-5">
                <h1 class="display-2 font-weight-bolder mb-4">Pepkor<br> Installations</h1>
                <div class="text-large font-weight-light">
                  Admin Portal
                </div>
            </div>
            <!-- /.Text -->

        </div>
        <!-- / Side container -->

        <!-- Form container -->
        <div class="d-flex col-lg-4 align-items-center bg-white p-5">
            <!-- Inner container -->
            <!-- Have to add `.d-flex` to control width via `.col-*` classes -->
            <div class="d-flex col-sm-7 col-md-5 col-lg-12 px-0 px-xl-4 mx-auto">
                <div class="w-100">

                    <!-- Logo -->
                    <div class="d-flex justify-content-center align-items-center">
                      <div class="">
                        <div class="w-100 position-relative" style="padding-bottom: 24%">
                            <img src="/assets/img/pep-logo.png" id = "login-logo"/>
                        </div>
                      </div>
                    </div>
                    <!-- / Logo -->

                    <h4 class="text-center text-lighter font-weight-normal mt-0 mb-0">Account Login</h4>

                    <!-- Form -->
                    <form class="my-5" role="form" action="/login/process" method="post" autocomplete="off">
                        <input type="hidden" name="process" value="true" />
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="text" name="email" class="form-control" required="">
                        </div>
                        <div class="form-group">
                            <label class="form-label d-flex justify-content-between align-items-end">
                                <div>Password</div>
                            </label>
                            <input type="password" name="password" class="form-control" required="">
                        </div>
                        <div class="d-flex justify-content-between align-items-center m-0">
                            <button type="submit" class="btn btn-primary ladda-button" data-style="zoom-out">Sign In</button>
                        </div>                     
                    </form>
                    <!-- / Form -->


                </div>
            </div>
        </div>
        <!-- / Form container -->

    </div>
</div>

  <!-- / Content -->


  
<?php echo view('_general/footer_javascript'); ?>

<script>
$(document).ready(function() {
  
    Ladda.bind( 'button', { timeout: 2000 } );

<?php if ($request->getGet('error') == '1') { ?>
    toastr["error"]("Incorrect Login Details!", "", {
        progressBar:       true
    });
<?php } ?>  
  
});
</script>


<?php echo view('_general/footer'); ?>      