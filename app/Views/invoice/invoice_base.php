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
         <div class="mb-1"><?php if(isset($pepkor_address['street'])) echo $pepkor_address['street'] ?></div>
         <div class="mb-1"><?php if(isset($pepkor_address['suburb'])) echo $pepkor_address['suburb'] ?></div>
         <div class="mb-1"><?php if(isset($pepkor_address['po_box'])) echo $pepkor_address['po_box'] ?></div>
         <div class="mb-1"><?php if(isset($pepkor_address['zip_code'])) echo $pepkor_address['zip_code'] ?></div>
      </div>
      <div class="col-sm-6 text-right pb-4">
         <h6 class="text-big text-large font-weight-bold mb-3">INVOICE #<?php if(isset($invoice['pastel_no'])) echo $invoice['pastel_no'];?></h6>
         <div class="mb-1">Date: <strong class="font-weight-semibold"><?php if(isset($invoice['created'])) echo $invoice['pastel_created'];?></strong></div>
         <div class="mb-1">Reg No: <strong class="font-weight-semibold"><?php if(isset($tax['reg_no'])) echo $tax['reg_no'] ?></strong></div>
         <div class="mb-1">VAT Reg NO: <strong class="font-weight-semibold"><?php if(isset($tax['vat_reg_no'])) echo $tax['vat_reg_no'] ?></strong></div>
         <div class="mb-1">Tel: <strong class="font-weight-semibold"><?php if(isset($pepkor_address['tel'])) echo $pepkor_address['tel'] ?></strong></div>
         <div class="mb-1">Fax: <strong class="font-weight-semibold"><?php if(isset($pepkor_address['fax'])) echo $pepkor_address['fax'] ?></strong></div>
      </div>
   </div>
   <hr class="mb-4">
   <div class="row">
      <div class="col-sm-6 mb-4">
         <div class="font-weight-bold mb-2"></div>
         <table class = "table table-borderless">
            <tbody>
               <tr>
                  <td class="pr-3"><?php if(isset($store['type'])) echo $store['type']; ?> Division of Pepkor Trading - <?php if(isset($job['type'])) echo $job['type']; ?></td>
               </tr>
               <!-- //TODO: What is this address? Possibly the Store type/Branch address -->
               <!-- <tr>
                  <td class="pr-3">PO Box 6376</td>
               </tr>
               <tr>
                  <td class="pr-3">Parrow East</td>
               </tr>
               <tr>
                  <td class="pr-3">7501</td>
               </tr> -->
            </tbody>
         </table>
      </div>
      <div class="col-sm-6 mb-4">
         <table class="table table-borderless">
            <tbody>
               <tr>
                  <td>
                     <div class="font-weight-bold mb-2">Delivered to:</div>
                  </td>
               </tr>
               <tr>
                  <td class="pr-3"><?php if(isset($store['brand'])) echo $store['brand'] ?></td>
               </tr>
               <tr>
                  <td class="pr-3"><?php if(isset($store['id'])) echo $store['id']." "; if(isset($store['name'])) echo $store['name'];?></td>
               </tr>
               </tr>
            </tbody>
         </table>
      </div>
   </div>
   <hr class="mb-4">
   <div class="row">
      <div class="table-responsive table-borderless">
         <table class="table m-0">
            <thead>
               <tr>
                  <th class="py-3">
                     Account
                  </th>
                  <th class="py-3">
                     Order Number
                  </th>
                  <th class="py-3">
                     Tax Reference
                  </th>
                  <th class="py-3">
                  </th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td class="py-3"><?php if(isset($invoice['account'])) echo $invoice['account']; ?></td>
                  <td class="py-3"><?php if(isset($order['no'])) echo $order['no']; ?></td>
                  <td class="py-3"><?php if(isset($invoice['tax_reference'])) echo $invoice['tax_reference']; ?></td>
                  <td class="py-3">Exclusive</td>
               </tr>
            </tbody>
         </table>
      </div>
   </div>
  <br><br>
   <div class="row">
      <div class="table-responsive mb-4">
         <table class="table mb-4">
         <div id=source-card-container>
             <!-- JS Loads all quotes and voc's as individual cards here -->
          </div>
               <tr>
                   <td>
                        <br><div class="font-weight-bold">Bank Details</div><br>
                        Bank name:<br>
                        Country:<br>
                        Branch:<br>
                        Account:<br>
                    </td>
                    <td>
                        <br><br><br><?php if(isset($pepkor_billing['bank'])) echo $pepkor_billing['bank'] ?><br>
                        <?php if(isset($pepkor_billing['country'])) echo $pepkor_billing['country'] ?><br>
                        <?php if(isset($pepkor_billing['branch'])) echo $pepkor_billing['branch'] ?><br>
                        <?php if(isset($pepkor_billing['account'])) echo $pepkor_billing['account'] ?>
                    </td>
                    <td colspan="4"> </td>
                    <td colspan="6" class="text-right py-3">
                    <table class = "table table-borderless">
                        <tbody>
                            <tr>
                                <td class="pr-3">Sub Total</td>
                                <td class="pr-3" id="sub-total">ZAR <?php if(isset($amount['sub_total'])) echo number_format($amount['sub_total'],2); else echo 0.00;?></td>
                            </tr>
                            <tr>
                                <td class="pr-3">Discount @ <?php if(isset($discount_percentage)) echo $discount_percentage; else echo 0;?>%</td>
                                <td class="pr-3" id="discount-total">ZAR <?php if(isset($amount['discount'])) echo number_format($amount['discount'],2); else echo 0.00;?></td>
                            </tr>
                            <tr>
                                <td class="pr-3">Amount Excl Tax</td>
                                <td class="pr-3" id="tax-excl-amount">ZAR <?php if(isset($amount['excl_tax'])) echo number_format($amount['excl_tax'],2); else echo 0.00;?></td>
                            </tr>
                            <tr>
                                <td class="pr-3">Tax @ <?php if(isset($tax_percentage)) echo $tax_percentage; else echo 0;?>%</td>
                                <td class="pr-3" id="tax-amount">ZAR <?php if(isset($amount['tax'])) echo number_format($amount['tax'],2); else echo 0.00;?></td>
                            </tr>
                            <tr>
                                <td class="pr-3">Total</td>
                                <td class="pr-3" ><strong class="font-weight-semibold">ZAR <?php if(isset($amount['total'])) echo number_format($amount['total'],2);else echo 0.00;?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                    </td>
                    
                </tr>
            </tbody>
         </table>
      </div>
   </div>
   <br>
</div>
