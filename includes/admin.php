<?php
// Render the admin settings page
function resume_review_render_admin_page()
{
    ?>
    <div class="wrap">
        <h1>Resume Review Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('resume_review_settings');
            do_settings_sections('resume_review_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register admin settings and fields
function resume_review_register_settings()
{
    register_setting('resume_review_settings', 'resume_review_settings');

    add_settings_section('resume_review_general_section', 'General Settings', '', 'resume_review_settings');

    add_settings_field('resume_review_allowed_filetypes', 'Allowed File Types', 'resume_review_allowed_filetypes_callback', 'resume_review_settings', 'resume_review_general_section');
    register_setting('resume_review_settings', 'resume_review_allowed_filetypes');

    add_settings_field('resume_review_mailto', 'Email to Receive Submissions', 'resume_review_mailto_callback', 'resume_review_settings', 'resume_review_general_section');
    register_setting('resume_review_settings', 'resume_review_mailto');

    add_settings_field('resume_review_mailfrom', 'Email Sender', 'resume_review_mailfrom_callback', 'resume_review_settings', 'resume_review_general_section');
    register_setting('resume_review_settings', 'resume_review_mailfrom');

    add_settings_field('resume_review_redirect_page', 'Redirect Page (Optional)', 'resume_review_redirect_page_callback', 'resume_review_settings', 'resume_review_general_section');
    register_setting('resume_review_settings', 'resume_review_redirect_page');
}

// Callback function for 'Allowed File Types' field
function resume_review_allowed_filetypes_callback()
{
    $allowed_filetypes = get_option('resume_review_allowed_filetypes', array());
    $available_filetypes = array('.pdf', '.doc', '.docx');

    foreach ($available_filetypes as $filetype) {
        $checked = in_array($filetype, $allowed_filetypes) ? 'checked' : '';
        $filetype_name = substr($filetype, 1); // Remove the leading dot

        echo '<label><input type="checkbox" name="resume_review_allowed_filetypes[]" value="' . esc_attr($filetype) . '" ' . $checked . '> ' . esc_html($filetype_name) . '</label><br>';
    }
}

// Callback function for 'Email to Receive Submissions' field
function resume_review_mailto_callback()
{
    $mailto = get_option('resume_review_mailto');
    ?>
    <input type="email" name="resume_review_mailto" value="<?php echo esc_attr($mailto); ?>" required>
    <?php
}

// Callback function for 'Email Sender' field
function resume_review_mailfrom_callback()
{
    $mailfrom = get_option('resume_review_mailfrom');
    ?>
    <input type="email" name="resume_review_mailfrom" value="<?php echo esc_attr($mailfrom); ?>" required>
    <?php
}

// Callback function for 'Redirect Page' field
function resume_review_redirect_page_callback()
{
    $redirect_page = get_option('resume_review_redirect_page');
    ?>
    <select name="resume_review_redirect_page">
        <option value="">None</option>
        <?php
        $pages = get_pages();
        foreach ($pages as $page) {
            $selected = ($page->ID == $redirect_page) ? 'selected' : '';
            echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
        }
        ?>
    </select>
    <?php
}


// Add the admin settings page
function resume_review_add_admin_page()
{
    add_menu_page(
        'Resume Review Setting', 
        'Resume Review', 
        'manage_options',
        'resume-review-settings', 
        'resume_review_render_admin_page', 
        'dashicons-media-default', 
        10 
    );

    resume_review_register_settings(); 
}
add_action('admin_menu', 'resume_review_add_admin_page');
