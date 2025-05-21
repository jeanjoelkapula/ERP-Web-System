<!-- Layout navbar -->
<nav class="layout-navbar navbar navbar-expand-lg align-items-lg-center navbar-expand-sm navbar-expand-xs align-items-sm-left bg-light container-p-x" id="layout-navbar">
	<?php $ionAuth = new \IonAuth\Libraries\IonAuth(); ?>
	<div class="navbar-collapse collapse" id="layout-navbar-collapse">
		<!-- Divider -->
		<!-- Sidenav toggle (see assets/css/demo/demo.css) -->
		<div class="layout-sidenav-toggle navbar-nav d-lg-none align-items-sm-left align-items-xs-left mr-auto float-left">
			<a class="nav-item nav-link px-0 mr-lg-4" href="javascript:void(0)">
			<i class="ion ion-md-menu text-large align-middle"></i>
			</a>
		</div>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#layout-navbar-collapse">
			<span class="navbar-toggler-icon"></span>
		</button>
  
    <!-- Logo -->
    <div class="d-flex justify-content-left align-items-left">
      <div class="">
        <div class="w-100 position-relative" style="padding-bottom: 2%">
            <img src="/assets/img/aquainstall.png" id = "login-logo"/>
        </div>
      </div>
    </div>
    <!-- / Logo -->
		<div class="navbar-nav align-items-lg-center ml-auto">
			<div class="demo-navbar-messages nav-item dropdown mr-lg-3">
			</div>
			<!-- Divider -->
			<div class="nav-item d-none d-lg-block text-big font-weight-light line-height-1 opacity-25 mr-3 ml-1">|</div>
			<div class="demo-navbar-user nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
				<span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
				<span class="px-1 mr-lg-2 ml-2 ml-lg-0"><?php echo $ionAuth->user()->row()->first_name?> <?php echo $ionAuth->user()->row()->last_name?></span>
				</span>
				</a>
				<div class="dropdown-menu dropdown-menu-right">
					<a href="/team/member/<?php echo $ionAuth->user()->row()->id?>" class="dropdown-item"><i class="ion ion-ios-person text-lightest"></i> &nbsp; My profile</a>
					<div class="dropdown-divider"></div>
					<a href="/login/logout" class="dropdown-item"><i class="ion ion-ios-log-out text-danger"></i> &nbsp; Log Out</a>
				</div>  
			</div>
		</div>
	</div>
</nav>
<!-- / Layout navbar -->