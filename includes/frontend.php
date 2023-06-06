<?php
// Enqueue frontend scripts and styles
function resume_review_enqueue_scripts()
{
    wp_enqueue_script('resume-review-frontend', plugin_dir_url(__FILE__) . '/js/frontend.js', array('jquery'), '1.0', true);
    wp_enqueue_style('resume-review-style', plugin_dir_url(__FILE__) . '/css/style.css', array(), '1.0');
}
add_action('wp_enqueue_scripts', 'resume_review_enqueue_scripts');


// Render the frontend form
function resume_review_render_form()
{
    ob_start();
?>
    <div id="resume-review-container">
        <form id="resume-review-form" method="post" enctype="multipart/form-data">
            <div class="fileUpload">
                <div class="uploadButton">
                    <img src="<?php echo plugin_dir_url(__FILE__) . '/images/upload-button.svg'; ?>" alt="">
                </div>
                <input type="file" name="resume" id="resume" required accept="<?php echo implode(',', get_option('resume_review_allowed_filetypes', array('.pdf', '.doc', '.docx'))); ?>">
                <label class="fileUploadLabel" for="resume">Get a free resume review</label>
            </div>
            <div class="mailPopUp" style="display: none;">
                <span class="topInfo">NEXT STEPS</span>
                <div class="lineDivider"></div>
                <h3 class="title">Where shall we send your critique?</h3>
                <p class="description">We’ll get you a detailed analysis of your resume within 48 hours. Now, we just need to know where to send it!</p>
                <label for="email">Email Address</label>
                <div class="mailFLex">
                    <input type="email" name="email" placeholder="email@website.com" required>
                    <input type="submit" value="Get my critique">
                </div>
                <div class="footerText">
                    <p> By uploading my resume, I agree to the TopResume <a href="#">Terms of Use</a> and acknowledge I have read the <a href="#">Privacy Policy.</a></p>
                </div>
            </div>
        </form>
        <div id="resume-review-message" style="display: none;"></div>
        <div id="bgLightbox" style="display: none;"></div>
    </div>
    <?php
    $max_upload_size = wp_max_upload_size() / 1024 / 1024; // Convert bytes to MB
    $allowed_types = implode(', ', get_allowed_mime_types());
    ?>
    <script>
        var maxFileSize = <?php echo $max_upload_size; ?>;
        var allowedFileTypes = "<?php echo $allowed_types; ?>";
    </script>
<?php
    return ob_get_clean();
}
add_shortcode('resume_review_form', 'resume_review_render_form');


// Handle form submission
function resume_review_handle_form_submission()
{
    if (isset($_FILES['resume']) && isset($_POST['email'])) {
        $resume_file = $_FILES['resume'];
        $email = sanitize_email($_POST['email']);

        // Check if the file upload was successful
        if ($resume_file['error'] === UPLOAD_ERR_OK) {
            $upload_dir = wp_upload_dir();
            $target_dir = trailingslashit($upload_dir['basedir']) . 'resume-review/';

            // Create the directory if it doesn't exist
            if (!is_dir($target_dir)) {
                wp_mkdir_p($target_dir);
            }

            $file_name = basename($resume_file['name']);
            $target_file = trailingslashit($target_dir) . $file_name;

            // Check if the file type is allowed
            $allowed_filetypes = get_option('resume_review_allowed_filetypes', array('.pdf', '.doc', '.docx'));
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if (!in_array('.' . $file_extension, $allowed_filetypes)) {
                echo '<p>This file type is not allowed.</p>';
                return; // Quit the submission
            }

            // Move the uploaded file to the target directory
            if (move_uploaded_file($resume_file['tmp_name'], $target_file)) {
                // File upload successful, proceed with further actions

                // Send email notification with attachment
                $mailto = get_option('resume_review_mailto');
                $mailfrom = get_option('resume_review_mailfrom');

                $subject = 'New Resume Submission';
                $message = "Email: $email\n\n";
                $message .= "Please find the attached resume.";

                $attachments = array($target_file);

                $headers = array(
                    'From: ' . $mailfrom,
                    'Content-Type: text/html; charset=UTF-8'
                );

                wp_mail($mailto, $subject, $message, $headers, $attachments);

                // Display success message or perform redirection
                $redirect_page = get_option('resume_review_redirect_page');
                if (!empty($redirect_page)) {
                    wp_redirect(get_permalink($redirect_page));
                    exit;
                } else {
                    echo '<p>Resume uploaded successfully.</p>';
                }
            } else {
                echo '<p>Error occurred while uploading the file.</p>';
            }
        } else {
            echo '<p>Error occurred during file upload. Please try again.</p>';
        }
    }
}
add_action('init', 'resume_review_handle_form_submission');
