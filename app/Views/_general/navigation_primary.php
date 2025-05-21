<?php
	$request = \Config\Services::request();
	$ionAuth = new \IonAuth\Libraries\IonAuth();
	$sgmts = $request->uri->getSegments();
	
	$s1 = isset($sgmts[0]) ? $sgmts[0] : '';
	$s2 = isset($sgmts[1]) ? $sgmts[1] : '';
	$s3 = isset($sgmts[2]) ? $sgmts[2] : '';
	$s4 = isset($sgmts[3]) ? $sgmts[3] : '';
	
?>
<ul class="sidenav-inner py-1">
<!-- only allow admin users to view user section -->
<?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager')) { ?>
    <!-- MENU TAB -->
    <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'team')?>">
        <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
            <i class="sidenav-icon far fa-users"></i>
            <div>Users</div>
        </a>
        <!-- MENU TAB ITEM -->
        <ul class="sidenav-menu">
            <li class="sidenav-item <?php _mnu($request->uri,'team/search')?>">
              <a href="/team/search" class="sidenav-link <?php _mnu($request->uri,'team/search')?>"><div>Search</div></a>
            </li>
       
            <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager')) { ?>
        <!-- MENU TAB ITEM -->
       
            <li class="sidenav-item <?php _mnu($request->uri,'team/create')?>">
              <a href="/team/create" class="sidenav-link <?php _mnu($request->uri,'team/create')?>"><div>Create</div></a>
            </li>

            <?php } ?>
        </ul>
    </li>  

    <li class="sidenav-divider mb-1"></li>    
<?php } ?>


    <!-- MENU TAB -->
    <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'division')?>">
        <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
            <i class="sidenav-icon far fa-sitemap"></i>
            <div>Divisions</div>
        </a>

        <!-- MENU TAB ITEM -->
        <ul class="sidenav-menu">
            <li class="sidenav-item <?php _mnu($request->uri,'division/search')?>">
              <a href="/division/search" class="sidenav-link <?php _mnu($request->uri,'division/search')?>"><div>Search</div></a>
            </li>
        
            <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager')) { ?>
                <li class="sidenav-item <?php _mnu($request->uri,'division/create')?>">
                <a href="/division/create" class="sidenav-link <?php _mnu($request->uri,'division/create')?>"><div>Create</div></a>
                </li>
            <?php } ?>
        </ul>
    </li> 

    <li class="sidenav-divider mb-1"></li> 
    
    <!-- MENU TAB -->
    <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'region')?>">
        <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
            <i class="sidenav-icon far fa-map"></i>
            <div>Regions</div>
        </a>

        <!-- MENU TAB ITEM -->
        <ul class="sidenav-menu">
            <li class="sidenav-item <?php _mnu($request->uri,'region/search')?>">
              <a href="/region/search" class="sidenav-link <?php _mnu($request->uri,'region/search')?>"><div>Search</div></a>
            </li>
        
            <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager')) { ?>
                <li class="sidenav-item <?php _mnu($request->uri,'region/create')?>">
                <a href="/region/create" class="sidenav-link <?php _mnu($request->uri,'region/create')?>"><div>Create</div></a>
                </li>
            <?php } ?>
        </ul>
    </li>

    <li class="sidenav-divider mb-1"></li> 

    <!-- MENU TAB -->
    <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'area')?>">
        <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
            <i class="sidenav-icon far fa-map-marked-alt"></i>
            <div>Areas</div>
        </a>

        <!-- MENU TAB ITEM -->
        <ul class="sidenav-menu">
            <li class="sidenav-item <?php _mnu($request->uri,'area/search')?>">
              <a href="/area/search" class="sidenav-link <?php _mnu($request->uri,'area/search')?>"><div>Search</div></a>
            </li>
        
            <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager')) { ?>
                <li class="sidenav-item <?php _mnu($request->uri,'area/create')?>">
                <a href="/area/create" class="sidenav-link <?php _mnu($request->uri,'area/create')?>"><div>Create</div></a>
                </li>
            <?php } ?>
        </ul>

    </li>

    <li class="sidenav-divider mb-1"></li> 

    <!-- MENU TAB -->
    <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'hub')?>">
        <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
            <i class="sidenav-icon far fa-bezier-curve"></i>
            <div>Hubs</div>
        </a>

        <!-- MENU TAB ITEM -->
        <ul class="sidenav-menu">
            <li class="sidenav-item <?php _mnu($request->uri,'hub/search')?>">
              <a href="/hub/search" class="sidenav-link <?php _mnu($request->uri,'hub/search')?>"><div>Search</div></a>
            </li>
        
            <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager')) { ?>
                <li class="sidenav-item <?php _mnu($request->uri,'hub/create')?>">
                <a href="/hub/create" class="sidenav-link <?php _mnu($request->uri,'hub/create')?>"><div>Create</div></a>
                </li>
            <?php } ?>
        </ul>
    </li>  

    <li class="sidenav-divider mb-1"></li> 

    <!-- MENU TAB -->
    <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('electrical_administrator')) { ?>
        <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'contractor')?>">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                <i class="sidenav-icon far fa-hands-helping"></i>
                <div>Contractors</div>
            </a>

            <!-- MENU TAB ITEM -->
            <ul class="sidenav-menu">
                <li class="sidenav-item <?php _mnu($request->uri,'contractor/search')?>">
                <a href="/contractor/search" class="sidenav-link <?php _mnu($request->uri,'contractor/search')?>"><div>Search</div></a>
                </li>
        
                <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager')) { ?>
                    <li class="sidenav-item <?php _mnu($request->uri,'contractor/create')?>">
                    <a href="/contractor/create" class="sidenav-link <?php _mnu($request->uri,'contractor/create')?>"><div>Create</div></a>
                    </li>
                <?php } ?>
            </ul>
        </li>
      

    <li class="sidenav-divider mb-1"></li> 
    <?php } ?>

    <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('electrical_administrator')) { ?>
        <!-- MENU TAB -->
        <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'store')?>">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                <i class="sidenav-icon far fa-shopping-basket"></i>
                <div>Stores</div>
            </a>

            <!-- MENU TAB ITEM -->
            <ul class="sidenav-menu">
                <li class="sidenav-item <?php _mnu($request->uri,'store/search')?>">
                    <a href="/store/search" class="sidenav-link <?php _mnu($request->uri,'store/search')?>"><div>Search</div></a>
                </li>
                
            
                <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager')) { ?>
                    <li class="sidenav-item <?php _mnu($request->uri,'store/create')?>">
                        <a href="/store/create" class="sidenav-link <?php _mnu($request->uri,'store/create')?>"><div>Create</div></a>
                    </li>
                <?php } ?>
            </ul>
            
        </li>  

        <li class="sidenav-divider mb-1"></li>
    <?php } ?>
    <!-- MENU TAB -->
    <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'stock')?>">
        <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
            <i class="sidenav-icon far fa-chart-bar"></i>
            <div>Stock</div>
        </a>
        <!-- MENU TAB ITEM -->
        <ul class="sidenav-menu">
            <li class="sidenav-item <?php _mnu($request->uri,'stock/search')?>">
              <a href="/stock/search" class="sidenav-link"><div>Stock Sheet</div></a>
            </li>
        
            <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('stock_controller')) { ?>
                <li class="sidenav-item">
                <a href="/stock/create" class="sidenav-link"><div>Create</div></a>
                </li>
                <li class="sidenav-item">
                    <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                        <div>Requisition</div>
                    </a>              
                    <ul class="sidenav-menu">
                        <li class="sidenav-item">
                            <a href="/requisition/search" class="sidenav-link"><div>Search</div></a>
                        </li>
                        <li class="sidenav-item">
                            <a href="/requisition/create" class="sidenav-link"><div>Create</div></a>
                        </li>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </li>  
    
    <li class="sidenav-divider mb-1"></li>

     <!-- MENU TAB -->
     <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('stock_controller')) { ?>
        <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'journal')?>">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                <i class="sidenav-icon far fa-clipboard-list"></i>
                <div>Stock Journals</div>
            </a>

            <!-- MENU TAB ITEM -->
            <ul class="sidenav-menu">
                <li class="sidenav-item <?php _mnu($request->uri,'journal/search')?>">
                    <a href="/journal/search" class="sidenav-link <?php _mnu($request->uri,'journal/search')?>"><div>Search</div></a>
                </li>
                
            

                <li class="sidenav-item <?php _mnu($request->uri,'journal/create')?>">
                    <a href="/journal/create" class="sidenav-link <?php _mnu($request->uri,'journal/create')?>"><div>Create</div></a>
                </li>
            </ul>
            
        </li>  

        <li class="sidenav-divider mb-1"></li>
    <?php } ?>

<?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('stock_controller')) { ?>
    <!-- MENU TAB -->
    <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'grn')?>">
        <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
            <i class="sidenav-icon far fa-clipboard-list"></i>
            <div>GRN</div>
        </a>

        <!-- MENU TAB ITEM -->
        <ul class="sidenav-menu">
            <li class="sidenav-item <?php _mnu($request->uri,'grn/search')?>">
                <a href="/grn/search" class="sidenav-link <?php _mnu($request->uri,'grn/search')?>"><div>Search</div></a>
            </li>
            
        

            <li class="sidenav-item <?php _mnu($request->uri,'grn/create')?>">
                <a href="/grn/create" class="sidenav-link <?php _mnu($request->uri,'grn/create')?>"><div>Create</div></a>
            </li>
        </ul>
        
    </li>  

    <li class="sidenav-divider mb-1"></li>
<?php } ?>

<?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager')) { ?>
        <!-- MENU TAB -->
        <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'job')?>">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                <i class="sidenav-icon far fa-wrench"></i>
                <div>Jobs</div>
            </a>
            <!-- MENU TAB ITEM -->
            <ul class="sidenav-menu">
                <li class="sidenav-item <?php _mnu($request->uri,'job/search/')?>">
                <a href="/job/search" class="sidenav-link"><div>Search</div></a>
                </li>
                <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager')) { ?>
                <li class="sidenav-item <?php _mnu($request->uri,'job/create/')?>">
                <a href="/job/create"class="sidenav-link"><div>Create</div></a>
                </li>

                <li class="sidenav-item <?php _mnu($request->uri,'job/search/job_types')?>">
                <a href="/job/search/job_types"class="sidenav-link"><div>Job Types</div></a>
                </li>
                <?php } ?>
                

            </ul>
        </li>  

        <li class="sidenav-divider mb-1"></li>
<?php } ?>
<?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('electrical_administrator')) { ?>

     <!-- MENU TAB -->
     <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'quote')?>">
        <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
            <i class="sidenav-icon far fa-newspaper"></i>
            <div>Quotes</div>
        </a>
        <!-- MENU TAB ITEM -->
        <ul class="sidenav-menu">
            <li class="sidenav-item <?php _mnu($request->uri,'quote/search')?>">
              <a href="/quote/search" class="sidenav-link"><div>Search</div></a>
            </li>
        
            <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('electrical_administrator')) { ?>
                <li class="sidenav-item">
                <a href="/quote/create" class="sidenav-link" <?php _mnu($request->uri,'quote/create')?>><div>Create</div></a>
                </li>

                <li class="sidenav-item">
                <a href="/quotecategory/search" class="sidenav-link" <?php _mnu($request->uri,'quotecategory/search')?>><div>Quote Categories</div></a>
                </li>
            <?php } ?>
        </ul>
    </li>

    <li class="sidenav-divider mb-1"></li>

<?php } ?>
<?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('electrical_administrator')) { ?>
    <!-- MENU TAB -->
    <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'invoice')?>">
        <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
            <i class="sidenav-icon far fa-file-invoice-dollar"></i>
            <div>Invoices</div>
        </a>
        <!-- MENU TAB ITEM -->
        <ul class="sidenav-menu">
            <li class="sidenav-item <?php _mnu($request->uri,'invoice/search')?>">
              <a href="/invoice/search" class="sidenav-link"><div>Search</div></a>
            </li>
        
        <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager')) { ?>
            <li class="sidenav-item" > 
              <a href="/invoice/create" class="sidenav-link"><div>Create</div></a>
            </li>
        <?php } ?>
        </ul>
    </li> 
    <li class="sidenav-divider mb-1"></li>
    <?php } ?>

    <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('electrical_administrator')) { ?>
        <!-- MENU TAB -->
        <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'order')?>">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                <i class="sidenav-icon far fa-archive"></i>
                <div>Orders</div>
            </a>
            <!-- MENU TAB ITEM -->
            <ul class="sidenav-menu">
                <li class="sidenav-item <?php _mnu($request->uri,'order/search')?>">
                <a href="/order/search" class="sidenav-link"><div>Search</div></a>
                </li>
            
                <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager')) { ?>
                    <li class="sidenav-item <?php _mnu($request->uri,'order/create')?>">
                        <a href="/order/create" class="sidenav-link"><div>Create</div></a>
                    </li>

                    <li class="sidenav-item <?php _mnu($request->uri,'order/create/create_internal')?>">
                        <a href="/order/create/create_internal" class="sidenav-link"><div>Internal Order</div></a>
                    </li>
                <?php } ?>
            </ul>
        </li> 
        <li class="sidenav-divider mb-1"></li>
    <?php } ?>
    <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('stock_controller')) { ?>
    <!-- MENU TAB -->
        <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'purchase')?>">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                <i class="sidenav-icon far fa-cart-plus"></i>
                <div>Purchase Orders</div>
            </a>
            <!-- MENU TAB ITEM -->
            <ul class="sidenav-menu">
                <li class="sidenav-item <?php _mnu($request->uri,'purchase/search')?>">
                    <a href="/purchase/search" class="sidenav-link"><div>Search</div></a>
                </li>
            
                <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager')) { ?>
                    <li class="sidenav-item <?php _mnu($request->uri,'purchase/create')?>">
                        <a href="/purchase/create" class="sidenav-link"><div>Create</div></a>
                    </li>
                <?php } ?>
            </ul>
        </li>
        <li class="sidenav-divider mb-1"></li>
    <?php } ?>

    <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('stock_controller')) { ?>

        <!-- MENU TAB -->
        <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'packing')?>">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                <i class="sidenav-icon fas fa-truck"></i>
                <div>Packing Bills</div>
            </a>
            <!-- MENU TAB ITEM -->
            <ul class="sidenav-menu">
                <li class="sidenav-item <?php _mnu($request->uri,'packing/search')?>">
                    <a href="/packing/search" class="sidenav-link"><div>Search</div></a>
                </li>
            
                <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('stock_controller')) { ?>
                    <li class="sidenav-item <?php _mnu($request->uri,'packing/create')?>">
                        <a href="/packing/create" class="sidenav-link"><div>Create</div></a>
                    </li>
                <?php } ?>
            </ul>
        </li>

        <li class="sidenav-divider mb-1"></li>
    <?php } ?>
<?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('electrical_administrator')) { ?>
    <!-- MENU TAB -->
    <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'voc')?>">
        <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
            <i class="sidenav-icon far fa-list-alt d-block"></i>
            <div>VOC</div>
        </a>
        <!-- MENU TAB ITEM -->
        <ul class="sidenav-menu">
            <li class="sidenav-item <?php _mnu($request->uri,'voc/search')?>">
            <a href="/voc/search" class="sidenav-link <?php _mnu($request->uri,'voc/search')?>" ><div>Search</div></a>
            </li>
        
            <?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('electrical_administrator')) { ?>
            <li class="sidenav-item <?php _mnu($request->uri,'voc/create')?>">
            <a href="/voc/create" class="sidenav-link <?php _mnu($request->uri,'voc/create')?>" ><div>Create</div></a>
            </li>
            <?php } ?>
        </ul>
    </li>
    <li class="sidenav-divider mb-1"></li>
<?php } ?>
<?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('stock_controller')) { ?>

    <!-- MENU TAB -->
    <li class="sidenav-item small font-weight-semibold <?php _mnu($request->uri,'delivery')?>">
        <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
            <i class="sidenav-icon far fa-list-alt d-block"></i>
            <div>Delivery Notes</div>
        </a>
        <!-- MENU TAB ITEM -->
        <ul class="sidenav-menu">
            <li class="sidenav-item <?php _mnu($request->uri,'delivery/search')?>">
                <a href="/delivery/search" class="sidenav-link"><div>Search</div></a>
            </li>
        
        
            <li class="sidenav-item <?php _mnu($request->uri,'delivery/create')?>">
                <a href="/delivery/create" class="sidenav-link"><div>Create</div></a>
            </li>
        </ul>
    </li>
    <li class="sidenav-divider mb-1"></li>

<?php } ?>
<?php if($ionAuth->isAdmin($ionAuth->user()->row()->id) || $ionAuth->inGroup('electrical_manager') || $ionAuth->inGroup('stock_controller')) { ?>

    <!-- MENU TAB -->
    <li class="sidenav-item small font-weight-semibold">
        <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
            <i class="sidenav-icon far fa-chart-line d-block"></i>
            <div>Reports</div>
        </a>
        <!-- MENU TAB ITEM -->
        <ul class="sidenav-menu">
            <li class="sidenav-item <?php _mnu($request->uri,'stock/reports')?>">
            <a href="/stock/reports" class="sidenav-link"><div>Stock</div></a>
            </li>
        
        
        </ul>
    </li>
<?php } ?>

</ul>
