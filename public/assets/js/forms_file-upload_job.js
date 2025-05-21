// Dropzone
$('#dropzone').dropzone({
    parallelUploads: 4,
    maxFilesize:     50000,
    filesizeBase:    1000,
    addRemoveLinks:  true,
    uploadMultiple: true,
    autoProcessQueue: false,
    accept: function(file, done) {
        done();
    },
    init: function(){
        var myDropzone = this;
        myDropzone.autoDiscover = false;
        
        $("#upload-button").on('click', function(e){
          e.preventDefault();
          e.stopPropagation();
  
          if (myDropzone.getQueuedFiles().length > 0) {                        
          myDropzone.processQueue();  
          } else {                       
            Swal.fire('Error!','Please select files to upload above before clicking the upload button.','error');
          }  
        });
    
        myDropzone.on('sendingmultiple', function(data, xhr, formData) {
          formData.append('job', jQuery('#job').val());
          formData.append('upload', jQuery('#upload').val());
          console.log(data);
        });
  
        myDropzone.on('queuecomplete', function(files, response) {
          
  
          Swal.fire({
            title: 'File Upload',
            text: "Successfully uploaded files/s.",
            type: 'success',
            showCancelButton: false,
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
          }).then(function() {
            myDropzone.removeAllFiles();
            Swal.fire({
              title: 'File Upload',
              text: "Would you like to upload more files?",
              type: 'question',
              showCancelButton: true,
              confirmButtonText: 'Yes',
              cancelButtonText: 'No'
            }).then(function(dismiss) {
              if (typeof dismiss.value === 'undefined') {
                window.location.href=`/job/documents?job=${$('#job').val()}`;   
              } else
              {
                window.location.href=`/job/upload?job=${$('#job').val()}`; 
              }
            });
          })
        });
  
        myDropzone.on('addedfile', function(files, response) {
  
        });
  
        myDropzone.on('success', function(files, response) {
          
        });
  
        myDropzone.on('errormultiple', function(files, response) {
            // alert(response);
        });
    }
  });