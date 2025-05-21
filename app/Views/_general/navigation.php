<!-- Layout sidenav -->
<?php $ionAuth = new \IonAuth\Libraries\IonAuth(); ?>
<div id="layout-sidenav" class="layout-sidenav sidenav sidenav-vertical bg-sidenav-theme" >
  <div class="app-brand demo">
    <span class="app-brand-logo">
    </span>
    <a href="/" class="app-brand-text demo sidenav-text font-weight-normal ml-2"></a>
    <a href="javascript:void(0)" class="layout-sidenav-toggle sidenav-link text-large ml-auto">
      <i class="ion ion-md-menu align-middle"></i>
    </a>
  </div>

  <div class="sidenav-divider mt-0"></div>

    <?php
      echo view('_general/navigation_primary');
      ?>
</div>
<!-- / Layout sidenav -->