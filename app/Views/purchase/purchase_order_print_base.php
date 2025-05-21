
<?php 

$formatter = new \NumberFormatter( 'en_ZA', \NumberFormatter::CURRENCY );

?>
<div class="card-body p-5">
   <div class="row">
      <div class="col-sm-6 pb-4">
         <div class="media align-items-center mb-4">
            <div class="media-body text-big font-weight-bold ml-2">
               <!-- Logo -->
               <div class="d-flex">
                  <div class="">
                     <div class="w-100 position-relative" >
                        <img src="/assets/img/pep-logo-print.png" id = "pep-logo-print"/>
                     </div>
                  </div>
               </div>
               <!-- / Logo -->
            </div>
         </div>
         <div class="mb-1">5 Bertie Avenu</div>
         <div class="mb-1">Epping II</div>
         <div class="mb-1">PO Box 863</div>
         <div class="mb-1">Epping, 7475</div>
      </div>
      <div class="col-sm-6 text-right pb-4">            
            <h6 class="text-big text-large font-weight-bold mb-3">PURCHASE ORDER #<?php echo $purchase_order_id; ?></h6>
            <div style="display:block">
                <div style="display:flex; flex-direction:row; float:right;">
                    <div class="text-right py-4">
                        <div class="mb-1">Order Date:&nbsp;&nbsp;&nbsp;&nbsp;</div>
                        <div class="mb-1">Date Required:&nbsp;&nbsp;&nbsp;&nbsp;</div>
                        <div class="mb-1">Ship Via:&nbsp;&nbsp;&nbsp;&nbsp;</div>
                    </div>
                    <div class="text-left py-4">
                        <div class="mb-1"><strong class="font-weight-semibold"><?php echo $order_date; ?></strong></div>
                        <div class="mb-1"><strong class="font-weight-semibold"><?php echo $date_required; ?></strong></div>
                        <div class="mb-1"><strong class="font-weight-semibold"><?php echo $ship_via; ?></strong></div>
                    </div>
                </div>
            </div>
      </div>
   </div>
   <hr class="mb-4">
   <div class="row">
      <div class="col-sm-6 mb-4">
         <div class="font-weight-bold mb-2"><div class="font-weight-bold mb-2" style="padding: .625rem;">Ship To</div></div>
         <table class = "table table-borderless">
            <tbody>
               <tr>
                  <td class="pr-3">Pep Div of Pepkor Trading - Installation</td>
               </tr>
               <tr>
                  <td class="pr-3">PO Box 6376</td>
               </tr>
               <tr>
                  <td class="pr-3">Parrow East</td>
               </tr>
               <tr>
                  <td class="pr-3">7501</td>
               </tr>
            </tbody>
         </table>
      </div>
      <div class="col-sm-6 mb-4">
        <div class="font-weight-bold mb-2" style="padding: .625rem;">Vendor</div>
        <table class = "table table-borderless">
            <tbody>
                <tr>
                    <td class="pr-3"><?php echo $vendor_name; ?></td>
                </tr>
                <tr>
                    <td class="pr-3"><?php if (isset($vendor_address)) echo $vendor_address; ?></td>
                </tr>
                <tr>
                    <td class="pr-3"><?php if (isset($vendor_po_box)) echo $vendor_po_box; ?></td>
                </tr>
                <tr>
                    <td class="pr-3"><?php if (isset($vendor_zip_code)) echo $vendor_zip_code; ?></td>
                </tr>
            </tbody>
        </table>
      
      </div>
   </div>
   <hr class="mb-4">
   <div class="row">
      <div class="table-responsive">
         <table class="table m-0">
            <thead>
                <tr>
                    <th class="py-3">
                        Code
                    </th>
                    <th class="py-3">
                        Description
                    </th>
                    <th class="py-3">
                        Unit
                    </th>
                    <th class="py-3">
                        Quantity
                    </th>
                    <th class="py-3">
                        Unit Price
                    </th>
                    <th class="py-3">
                        Amount
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php 
                if (isset($order_stock)) {
                    foreach ($order_stock as $item) {
                ?>
                    <tr>
                        <td id='<?php echo $item['EBQ_CODE']; ?>' class="py-3"><?php echo $item['EBQ_CODE']; ?></td>
                        <td class="py-3"><?php echo $item['DESCRIPTION']; ?></td>
                        <td class="py-3"><?php echo $item['METRIC_DESCRIPTION']; ?></td>
                        <td class="py-3"><?php echo $item['QUANTITY']; ?></td>
                        <td class="py-3">R <?php echo number_format($item['UNIT_PRICE'], 2, '.', ','); ?></td>
                        <td class="py-3">R <?php echo number_format($item['AMOUNT'], 2, '.', ','); ?></td>
                    </tr>
                <?php 
                    };
                }
                ?>
                <tr>
                    <td colspan="5" class="text-right py-3">
                        <br><br>Misc Charges:<br>
                        <span class="d-block text-big mt-2 py-3">Freight Charges:</span>
                        <span class="d-block text-big mt-2 py-3">Total:</span>
                    </td>
                    <td class="py-3">
                        <br><br><strong><?php echo $formatter->format( $misc_charges ); ?></strong>
                        <span class="d-block text-big mt-2 py-3"><strong><?php echo $formatter->format( $freight_charges); ?></strong></span>
                        <strong class="d-block text-big mt-2 py-3">R <?php echo number_format($po_amount, 2, '.', ','); ?></strong>
                    </td>
                </tr>
            </tbody>
         </table>
      </div>
   </div>
  <br><br>
   <br>
</div>
