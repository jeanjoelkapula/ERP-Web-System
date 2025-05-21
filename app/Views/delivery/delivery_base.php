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
         <div class="mb-1">5 Bertie Avenue</div>
         <div class="mb-1">Epping II</div>
         <div class="mb-1">PO Box 863</div>
         <div class="mb-1">Epping, 7475</div>
      </div>
      <div class="col-sm-6 text-right pb-4">
         <h6 class="text-big text-large font-weight-bold mb-3">DELIVERY NOTE #<?php echo $entity_id; ?></h6>
         <div class="mb-1">Date: <strong class="font-weight-semibold"><?php echo $maintenancedate; ?></strong></div>
         <div class="mb-1">Reg No: <strong class="font-weight-semibold">1966/003645/07</strong></div>
         <div class="mb-1">VAT Reg NO: <strong class="font-weight-semibold">4390249037</strong></div>
         <div class="mb-1">Tel: <strong class="font-weight-semibold">021 534 3200</strong></div>
         <div class="mb-1">Fax: <strong class="font-weight-semibold">021 535 0921</strong></div>
         <div class="mb-1">Order No: <strong class="font-weight-semibold"><?php echo $ordernum; ?></strong></div>
         <div class="mb-1">Delivery Method: <strong class="font-weight-semibold"><?php echo $deliverymethod; ?></strong></div>
      </div>
   </div>
   <div class="row">
      <div class="table-responsive mb-4">
         <table class="table mb-4">
            <thead>
               <tr>
                  <th class="py-3">
                     Code
                  </th>
                  <th class="py-3">
                     Description
                  </th>
                  <th class="py-3">
                     Quantity
                  </th>
               </tr>
            </thead>
            <tbody>
            <!-- Loop through the order to display the contents of the order on the delivery note  -->
            <?php
            if (isset($orderObject)) {                  
               foreach ($orderObject->getResult('array') as $row): 
               {                           
                  echo "<tr><td class='py-3'>".$row['EBQ_CODE']."</td><td class='py-3'>".$row['DESCRIPTION']."</td><td class='py-3'>".$row['QUANTITY']."</td></tr>";
               } endforeach;              
            } ?>

            </tbody>
         </table>
      </div>
   </div>

   <div class="row">
		<div class="col-sm-6">   
         <div class="row">
            <strong>CUSTOMER'S SIGNATURE ON RECEIPT OF <br>GOODS IN GOOD CONDITION</strong>            
         </div>
		</div>
		<div class="col-sm-6 pb-4">   
         <div class="row">
            <br><strong>DATE:</strong>
         </div>
		</div>
   </div>
   <br>
   <br>
   <div class="row">
		<div class="col-sm-6">   
         <div class="row">
            .............................................................................................         
         </div>
		</div>
		<div class="col-sm-6 pb-4">   
         <div class="row">
         ..................................................  
         </div>
		</div>
   </div>
   
</div>
