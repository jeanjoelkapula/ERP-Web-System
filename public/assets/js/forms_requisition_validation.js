// With validation
$(function() {
    var $form = $('#form-requisition');
    var $btnFinish = $('<button class="btn-finish btn btn-primary hidden mr-2" type="button">Finish</button>');
  
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
            expected_date: "Please select the expected completion date for this requisition",
        }
    });
    // Click on finish button
    $form.find('#form-submit').on('click', function(e){
      if (!$form.valid()){ return; }
      var data = $form.serializeArray();
      var form_enable = data.find(item=> item.name ==='form_enable');
      var stockItem = document.querySelector('#requisition_item').value;
      //if (form_enable.value === 'true') {
        if(stockItem != ""){
          $form.submit();
          return false;
        }
        else{
          e.preventDefault();
          Swal.fire('Error!','Please choose the stock item to be requisitioned','error');
          return;
        }
      /*}
      else
      {
        Swal.fire('Error!','This purchase order has already been approved or declined and cannot modified','error');
      }*/      
    });
  });
    