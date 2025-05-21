<?php
$request = \Config\Services::request();
?>
<!-- Core scripts -->
<script src="/assets/vendor/libs/popper/popper.js"></script>
<script src="/assets/vendor/js/bootstrap.js"></script>
<script src="/assets/vendor/js/sidenav.js"></script>

<!-- Libs -->
<script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="/assets/vendor/libs/spin/spin.js"></script>
<script src="/assets/vendor/libs/ladda/ladda.js"></script>
<script src="/assets/vendor/libs/growl/growl.js"></script>
<script src="/assets/vendor/libs/toastr/toastr.js"></script>
<script src="/assets/vendor/libs/bootbox/bootbox.js"></script>



<script src="/assets/vendor/libs/loadingoverlay/loadingoverlay.min.js"></script>


<script src="/assets/vendor/libs/select2/select2.js"></script>
<script src="/assets/vendor/libs/autoNumeric/autoNumeric.min.js"></script>

<script src="/assets/vendor/libs/datatables/datatables.js"></script>
<script src="/assets/vendor/libs/validate/validate.js"></script>
<script src="/assets/vendor/libs/moment/moment.js"></script>
<script src="/assets/vendor/libs/smartwizard/smartwizard.js"></script>
<script src="/assets/js/forms_pickers.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- <script src="/assets/vendor/libs/flatpickr/flatpickr.js"></script> -->
<script src="/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>

<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/jquery.validate.min.js"></script>
<script src="/assets/vendor/libs/sweetalert2/js/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap4-duallistbox/4.0.2/jquery.bootstrap-duallistbox.js"></script>
<!--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCy52vI2201XJ4AJjHbxEc11VcY5te3pzU"></script>-->
<!-- Demo -->
<script src="/assets/js/demo.js"></script>
<script src="/assets/js/search_autocomplete.js"></script>
<script src="/assets/js/number_plus_minus.js"></script>
<script src="/assets/js/pages_file-manager.js"></script>
<script src="/assets/vendor/libs/dropzone/dropzone.js"></script>
<script src="/assets/js/onscan.js"></script>

<script src="/assets/js/ui_tooltips.js"></script>
<script>
    
var glb_html_default_loading = '<div style="text-align:center; padding:150px;"><i class="far fa-5x fa-spinner-third fa-spin"></i><h4 class="mt-5">loading...</h4></div>';    

function setActivTab(tab){
  $('.nav-tabs a[href="#tab-' + tab + '"]').tab('show');
};   


function no_access(){
 
    $.growl({
      title:   'No Access!',
      message: 'You don\'t have access!',
      size:    'large',
    });
 
}
    


function bind_form_actions(){
    
    Ladda.bind('.ladda-button');
    
    $('form.submit-once').submit(function(e){
        
        var btm_submit = $(this).find(':submit');
        btm_submit.attr('disabled', 'disabled');
       
        if( $(this).hasClass('form-submitted') ){
          e.preventDefault();
          $.growl({title: 'No Access!',message: 'You don\'t have access!', size: 'large'});
          return;
        }

        $(this).addClass('form-submitted');

    });
    
}

function removeEmojis (string) {

var regex = /(?:[\u2700-\u27bf]|(?:\ud83c[\udde6-\uddff]){2}|[\ud800-\udbff][\udc00-\udfff]|[\u0023-\u0039]\ufe0f?\u20e3|\u3299|\u3297|\u303d|\u3030|\u24c2|\ud83c[\udd70-\udd71]|\ud83c[\udd7e-\udd7f]|\ud83c\udd8e|\ud83c[\udd91-\udd9a]|\ud83c[\udde6-\uddff]|\ud83c[\ude01-\ude02]|\ud83c\ude1a|\ud83c\ude2f|\ud83c[\ude32-\ude3a]|\ud83c[\ude50-\ude51]|\u203c|\u2049|[\u25aa-\u25ab]|\u25b6|\u25c0|[\u25fb-\u25fe]|\u00a9|\u00ae|\u2122|\u2139|\ud83c\udc04|[\u2600-\u26FF]|\u2b05|\u2b06|\u2b07|\u2b1b|\u2b1c|\u2b50|\u2b55|\u231a|\u231b|\u2328|\u23cf|[\u23e9-\u23f3]|[\u23f8-\u23fa]|\ud83c\udccf|\u2934|\u2935|[\u2190-\u21ff])/g;

return string.replace(regex, '');

}

$(document).ready(function() {
    



});
</script>

<!--Gandalf-->