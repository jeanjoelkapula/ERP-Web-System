// With validation
$(function() {
  var $form = $('#purchase-order-wizard');
  var $btnFinish = $('<button class="btn-finish btn btn-primary hidden mr-2" type="button">Finish</button>');
  var actionType = $('#action_type').val();
  // Set up validator
  $form.validate({
      errorPlacement: function errorPlacement(error, element) {
      $(element).parents('.form-group').append(
          error.addClass('invalid-feedback small d-block')
      )
      },
      highlight: function(element) {
      $(element).addClass('is-invalid');
      },
      unhighlight: function(element) {
      $(element).removeClass('is-invalid');
      },
      rules: {
      
      },
      messages: {
          purchase_order_no: "Please enter a unique order number",
          order_date: "Please select the purchase order date",
          date_required: "Please select the date required",
          ship_via: "Please enter the means which the stock will be shipped",
          vendor_name: "Please enter the vendor name"
      }
  });
  
  // Initialize wizard
  $form
    .smartWizard({
      autoAdjustHeight: false,
      backButtonSupport: true,
      useURLhash: false,
      showStepURLhash: false,
      toolbarSettings: {
        toolbarExtraButtons: [$btnFinish]
      }
    })
    .on('leaveStep', function(e, anchorObject, stepNumber, stepDirection) {
      // stepDirection === 'forward' :- this condition allows to do the form validation
      // only on forward navigation, that makes easy navigation on backwards still do the validation when going next
      
      if(stepNumber == 0 && stepDirection == "forward" && actionType == "create" ){
        console.log('true');
        order_no = $('#purchase_order_no').val();
        $.ajax({
          url: "/purchase/create/ordercheck",
          type: 'post',
          data:{'purchase_order_no':order_no},
          cache: false,
          success: function(data){
            if (data == "true") {
              e.preventDefault();
              Swal.fire('Incomplete!','Please enter a unique order number!','error');
              $('.sw-btn-prev').click();
            }
  
          },
          error: function(data){
            e.preventDefault();
            Swal.fire('Incomplete!','Something went wrong please try again later','error');
          }
        });
        
      } 
      else {
        return true;
      }
    })
    .on('showStep', function(e, anchorObject, stepNumber, stepDirection) {
      var $btn = $form.find('.btn-finish');
      // Enable finish button only on last step
      if (stepNumber === 1) {
        $btn.removeClass('hidden');
        
      } else {
        $btn.addClass('hidden');
      }
    });
  
  // Click on finish button
  $form.find('.btn-finish').on('click', function(e){
    if (!$form.valid()){ return; }
    var data = $form.serializeArray();
    var form_enable = data.find(item=> item.name ==='form_enable');
    var stock = data.find(item=> item.name.substring(0,5)==="stock");
    if (form_enable.value === 'true') {
      if(stock){
        $form.submit();
        return false;
      }
      else{
        e.preventDefault();
        Swal.fire('Error!','The purchase order requires at least one stock item!','error');
        return;
      }
    }
    else
    {
      Swal.fire('Error!','This purchase order has already been approved or declined and cannot modified','error');
    }      
  });
});
  