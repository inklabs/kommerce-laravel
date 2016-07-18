'use strict';

$(function () {

  /**
   * Update elements after a file has been chosen to be uploaded
   */
  $('input:file').change(function () {
    beforeSubmitUpload();
    var fileName = $(this).val();
    $('#js-filename').html(fileName); // get the filename for the placeholder
    $('.custom-file-upload').hide(); // hide upload button
    $('#js-submit').show();
  });

  /**
   * Check file size before uploading.
   *
   * @return success|failure
   */
  function beforeSubmitUpload() {

    //check whether browser fully supports all File API
    if (window.File && window.FileReader && window.FileList && window.Blob) {

      if (!$('#js-file').val()) //check empty input filed
      {
        ErrorReset('You didn\'t upload anything.');
        return false;
      }

      var fsize = $('#js-file')[0].files[0].size; //get file size
      var ftype = $('#js-file')[0].files[0].type; // get file type

      // Allowed file size is less than 10 MB (10485760)
      if (fsize > 10485760) {
        ErrorReset('<b>' + bytesToSize(fsize) + '</b> Too big file! <br />File is too big, it should be less than 10MB.');
        return false;
      }

      $('#js-submit').hide();
      $('#js-output').show().html('');
    } else {
      // js-output error to older unsupported browsers that doesn't support HTML5 File API
      ErrorReset('Please upgrade your browser, your current browser lacks some new features we need!');
      return false;
    }

  }

  /**
   * Function to format bytes to bites
   * see: bit.ly/19yoIPO
   *
   * @param  integer bytes
   * @return integer bites
   */
  function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes === 0) return '0 Bytes';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
  }

  /**
   * Alert message when an error occurs
   *
   * @param string  message   Error Message Details
   */
  function ErrorReset(message) {
    $('#js-output').show().html('<p>' + message + '</p>');
    $('#js-submit').hide(); // hide submit button
    $('#js-reset').show(); // show reset button after
  }

});
