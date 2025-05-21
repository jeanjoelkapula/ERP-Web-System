<?php echo view('_general/header'); ?>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-2">
        <div class="layout-inner">

            <?php echo view('_general/navigation'); ?>

            <!-- Layout container -->
            <div class="layout-container">

                <?php echo view('_general/navigation_top'); ?>


                    <div class="text-center error-box">
 
                            <h1 class="error-text mt-5"><i class="fa fa-times-circle text-danger error-icon-shadow"></i> Error</h1>
                            <h2 class="font-xl"><strong>You don't have access, or the data no longer exists!</strong></h2>
                            <br />
                            <p class="lead semi-bold">
                                    <small>
                                            You were trying to view something that is not there or you do not have access to.
                                    </small>
                            </p>
                    </div>
 

            </div>
            <!-- / Layout container -->

        </div>
        <!-- Overlay -->
	<div class="layout-overlay layout-sidenav-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

<?php echo view('_general/footer_javascript'); ?>



<script>
$(document).ready(function() {



});
</script>

<?php echo view('_general/footer'); ?>