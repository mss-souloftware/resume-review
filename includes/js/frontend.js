jQuery(document).ready(function($) {
  $("#resume-review-form").submit(function(e) {
    e.preventDefault(); // Prevent the form from submitting

    var allowedFileTypes = ["pdf", "doc", "docx"]; // Add your allowed file types here

    var formData = new FormData(this);
    var resumeFile = formData.get("resume");

    if (!resumeFile) {
      return; // No file selected, quit the submission
    }

    var fileName = resumeFile.name;
    var fileExtension = fileName.split(".").pop().toLowerCase();

    if (allowedFileTypes.indexOf(fileExtension) === -1) {
      $("#resume-review-message").text("This file type is not allowed.");
      $("#resume-review-message").show();
      return; // Quit the submission
    }

    // Proceed with form submission
    this.submit();
  });

  // Show the error message instantly when a file is selected
  $("#resume-review-form input[name='resume']").on("change", function() {
    var fileName = $(this).val();
    var fileExtension = fileName.split(".").pop().toLowerCase();

    if (allowedFileTypes.indexOf(fileExtension) === -1) {
      $("#resume-review-message").text("This file type is not allowed.");
      $("#resume-review-message").show();
      $("#resume-review-container .mailPopUp").hide();
      $("#resume-review-container #bgLightbox").hide();
    } else {
      $("#resume-review-message").hide();
      $("#resume-review-container .mailPopUp").show();
      $("#resume-review-container #bgLightbox").show();
    }
  });
});
