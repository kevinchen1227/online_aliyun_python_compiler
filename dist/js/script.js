function output(text) {
	if (text =="clear")
		text = "";
	var txtArea ;
    txtArea = document.getElementById("output") ;
    txtArea.value +=  text + '\r\n';
	//$("output").val(text);
}  
$('#call-aliyun, #call-tencent, #call-aws, #call-gcp, .click-upload').click(function(e) {
	  console.log($(this).attr('id'));
	  //document.getElementById(this.id).setAttribute('class', 'upload--loading');
	  $($(this).attr('id')).addClass('upload--loading');
	  $('.upload-hidden').click();
	}); 

$('.upload-hidden').change(function() {
  //document.getElementById(e.target.id).classList.remove( 'upload--loading');
  $('.upload').removeClass('upload--loading');
  $('body').addClass('file-process-open');

	document.getElementById('output').value= "";
	console.log('output is cleared');
});
$('.file-upload-bar-closed').click(function() {
  $('body').removeClass('file-process-open');
});
$('.open-progress').click(function() {
  $('body').toggleClass('file-process-open');
  
});
/*
var settings = {
      'cache': false,
      'dataType': "json",
      "async": true,
      "crossDomain": true,
      //"url": url_upload,
      "method": "POST",
      "headers": {
          "accept": "application/json",
          "Access-Control-Allow-Origin":"*"
      }
  }

  $.ajax(settings).done(function (response) {
      console.log('ajax==>' + response);

  });
  $.ajax(settings).fail(function (response) {
      console.log('ajax==>' + response);

  });

*/
//$(function() {
$(document).ready(function() {
  var ul = $('#upload ul');

  $('#drop a').click(function() {
    // Simulate a click on the file input button
    // to show the file browser dialog
	
    $(this).parent().find('input').click();
	
  });
  
  //HC stage
  //var url_upload="http://218.32.248.9/file-upload/dist/app/upload.php";
  //kevin dev stage
 var url_upload="http://localhost:8888/file-upload/dist/app/upload.php";
 /*$("#call-to-action").uploadFile({
	url: url_upload,
	fileName:"myfile"
 }); */
/*
$('#upload').click(function(){
		$('drop').click();
		//console.log("here");
        var fd = new FormData();
        var files = $('#upl')[0].files;
		// This element will accept file drag/drop uploading
        console.log(files.length);// clear
        // Check file selected or not
        if(files.length > 0 ){
           fd.append('#upl',files[0]);
			console.log(files.length);// clear
           $.ajax({
              url: url_upload,
              type: 'post',
              data: fd,
              contentType: false,
              processData: false,
              success: function(response){
                 if(response != 0){
                    $("#img").attr("src",response); 
                    $(".preview img").show(); // Display image element
                 }else{
                    output('file not uploaded');
                 }
              },
           });
        //}else{
	
           //alert("Please select a file.");
		//	output("Please select a file.");
        }
    });
/*
$('#upload').fileupload({
    dataType: 'json',
    add: function (e, data) {            
        $("#up_btn").off('click').on('click', function () {
			console.log("submit")
            data.submit();
        });
    },
});
	
*/

// Initialize the jQuery File Upload plugin
$('#upload').fileupload({
	
    // This element will accept file drag/drop uploading
    dropZone: $('#drop'),
	url: url_upload,
	maxFileSize : 5000000,
    acceptFileTypes: /(\.|\/)(rar|zip)$/i,
	dataType: "json",
	sequentialUploads: true,
	/*
	beforeSend: function(xhr, data) {
   		console.log("in beforeSend");
		xhr.setRequestHeader('Access-Control-Allow-Headers', '*');
		xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
		
		//xhr.setRequestHeader('Access-Control-Request-Headers','x-requested-with');
		
  	}, */
    // This function is called when a file is added to the queue;
    // either via the browse button, or via drag/drop:
    add: function(e, data) {
	  console.log( e + "old fileupload");
	  
      var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48"' +
        ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');

      // Append the file name and file size
      tpl.find('p').text(data.files[0].name)
        .append('<i>' + formatFileSize(data.files[0].size) + '</i>');

      // Add the HTML to the UL element
      data.context = tpl.appendTo(ul);

      // Initialize the knob plugin
      tpl.find('input').knob();

      // Listen for clicks on the cancel icon
      tpl.find('span').click(function() {

        if (tpl.hasClass('working')) {
          jqXHR.abort();
        }

        tpl.fadeOut(function() {
          tpl.remove();
        });

      });

      // Automatically upload the file once it is added to the queue
      var jqXHR = data.submit();
    },

    progress: function(e, data) {

      // Calculate the completion percentage of the upload
      var progress = parseInt(data.loaded / data.total * 100, 10);

      // Update the hidden input field and trigger a change
      // so that the jQuery knob plugin knows to update the dial
      data.context.find('input').val(progress).change();

      if (progress == 100) {
        data.context.removeClass('working');
      }
    },

    fail: function(e, data) {
      // Something has gone wrong!
	  //var r = data.result;
	  //var afArray = JSON.parse(data);
      console.log('error=>' + data.jqXHR.responseText);
      //output(data.jqXHR.responseText);
	
    },

	done: function(e, data) {
        //var r = data.result;
        console.log('done=>' +data.jqXHR.responseText);
		//$('#output').val= "";
		//output(data.jqXHR.responseText);
    },
	always: function(e, data) {
        //var r = data.result;
		
        console.log('always=>' +data.jqXHR.responseText);
		output(data.jqXHR.responseText);
    },


  }); 

  // Prevent the default action when a file is dropped on the window
  $(document).on('drop dragover', function(e) {
    e.preventDefault();
  });

  // Helper function that formats the file sizes
  function formatFileSize(bytes) {
    if (typeof bytes !== 'number') {
      return '';
    }

    if (bytes >= 1000000000) {
      return (bytes / 1000000000).toFixed(2) + ' GB';
    }

    if (bytes >= 1000000) {
      return (bytes / 1000000).toFixed(2) + ' MB';
    }

    return (bytes / 1000).toFixed(2) + ' KB';
  }
  
	
});