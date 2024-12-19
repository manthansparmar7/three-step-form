<?php
/*
Plugin Name: 3-Step Form Plugin
Description: A custom plugin to create a 3-step form with clean UI, AJAX handling, and dynamic features.
Version: 1.0
Author: Manthan Parmar
*/

if (!defined('ABSPATH')) exit;

// Define constants
define('STEP_FORM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('STEP_FORM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Database table creation
function step_form_create_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'step_form_data';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        selected_skills text,
        selected_option varchar(255),
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        phone varchar(15) NOT NULL,
        agree tinyint(1) DEFAULT 0 NOT NULL,
        date_submitted datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'step_form_create_table');

// Include necessary files
require_once STEP_FORM_PLUGIN_DIR . 'includes/admin-display.php';

require_once STEP_FORM_PLUGIN_DIR . 'includes/form-handler.php';

require_once STEP_FORM_PLUGIN_DIR . 'includes/ajax-operations.php';

// Enqueue scripts and styles
function step_form_enqueue_assets() {
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    wp_enqueue_style('step-form-css', STEP_FORM_PLUGIN_URL . 'assets/css/style.css');
    wp_enqueue_style('intl-tel-input-css', 'https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.15/build/css/intlTelInput.min.css');

    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-validation-js', 'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js', ['jquery'], null, true);
    wp_enqueue_script('intl-tel-input-js', 'https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.15/build/js/intlTelInput.min.js', ['jquery'], null, true);
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', ['jquery'], null, true);
    wp_enqueue_script('step-form-js', STEP_FORM_PLUGIN_URL . 'assets/js/script.js', ['jquery', 'jquery-validation-js', 'intl-tel-input-js'], null, true);

    wp_localize_script('step-form-js', 'stepFormAjax', ['ajax_url' => admin_url('admin-ajax.php')]);
}
add_action('wp_enqueue_scripts', 'step_form_enqueue_assets');

// Shortcode for the form
function step_form_shortcode() {
    ob_start(); ?>
    <form id="step-form" method="post">
        <!-- Step 1: Skill Selection -->
        <div id="step-1" class="step">
            <div class="container">
                <div class="row">
                    <!-- Left Section -->
                    <div class="col-md-6 d-flex flex-column justify-content-center text-light bg-dark p-5">
                        <h2>Find your perfect match</h2>
                        <p>Answer 6 short questions to help us understand your needs.</p>
                        <ul class="advantages">
                            <li>
                                <img src="<?php echo STEP_FORM_PLUGIN_URL . 'assets/images/verified-icon.png'; ?>" alt="Verified" class="verified-icon">
                                Gain access to 5000+ experts
                            </li>
                            <li>
                                <img src="<?php echo STEP_FORM_PLUGIN_URL . 'assets/images/verified-icon.png'; ?>" alt="Verified" class="verified-icon">
                                Get matched with a developer in 2 days
                            </li>
                            <li>
                                <img src="<?php echo STEP_FORM_PLUGIN_URL . 'assets/images/verified-icon.png'; ?>" alt="Verified" class="verified-icon">
                                Hire quickly and easily with 94% match success
                            </li>
                        </ul>
                    </div>

                    <!-- Right Section -->
                    <div class="col-md-6 bg-white p-5">
                        <h4>Choose the skills you’re after</h4>
                        <p>You can either search for a skill or select from the popular skills below.</p>

                        <p><strong>Search skill</strong></p>
                        <!-- Search Skill Input -->
                        <div class="input-group mb-3">
                            <input type="text" id="search-skill" class="form-control" placeholder="Find over 500+ skills...">
                            <span class="input-group-text">
                                <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/magnifying-glass.png'; ?>" alt="Search" class="search-icon">
                            </span>
                        </div>

                        <!-- Selected Skills (Hidden input fields) -->
                        <div id="selected-skills" class="mt-3">
                            <div id="selected-skills-label"></div>
                            <ul id="selected-skills-list" class="list-unstyled">
                                <!-- Selected skills will appear here -->
                            </ul>
                        </div>
                        <input type="hidden" name="selected_skills" id="selected-skills-input" value="">

                        <!-- Skill Tags -->
                        <div id="skills-container" class="skill-tags">
                            <!-- Skills will be dynamically rendered here -->
                        </div>

                        <!-- Next Button -->
                        <div class="text-end mt-4">
                            <button class="btn btn-primary next-step" data-next="2">Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Select an Option -->
        <div id="step-2" class="step" style="display:none;">
            <div class="container">
                <div class="row">
                    <!-- Left Section: Text and Image -->
                    <div class="col-md-6 d-flex flex-column justify-content-center text-light bg-dark p-5">
                        <h2>Find your perfect match</h2>
                        <p>Answer 6 short questions to help us understand your needs.</p>
                        <ul class="advantages">
                            <li>
                                <img src="<?php echo STEP_FORM_PLUGIN_URL . 'assets/images/verified-icon.png'; ?>" alt="Verified" class="verified-icon">
                                Gain access to 5000+ experts
                            </li>
                            <li>
                                <img src="<?php echo STEP_FORM_PLUGIN_URL . 'assets/images/verified-icon.png'; ?>" alt="Verified" class="verified-icon">
                                Get matched with a developer in 2 days
                            </li>
                            <li>
                                <img src="<?php echo STEP_FORM_PLUGIN_URL . 'assets/images/verified-icon.png'; ?>" alt="Verified" class="verified-icon">
                                Hire quickly and easily with 94% match success
                            </li>
                        </ul>
                    </div>

                    <!-- Right Section: Radio Options -->
                    <div class="col-md-6 bg-white p-5">
                        <h3 class="mb-3">How long will the engagement last?</h3>
                        <div class="radio-options-container">
                            <div class="form-check mb-3" style="border: 1px solid lightgrey; padding: 10px; border-radius: 5px; display: flex; align-items: center;">
                                <input type="radio" name="option" value="More than 6 months" class="form-check-input" id="option1">
                                <label class="form-check-label" for="option1" style="margin-left: 10px;">More than 6 months</label>
                            </div>
                            <div class="form-check mb-3" style="border: 1px solid lightgrey; padding: 10px; border-radius: 5px; display: flex; align-items: center;">
                                <input type="radio" name="option" value="3 to 6 months" class="form-check-input" id="option2">
                                <label class="form-check-label" for="option2" style="margin-left: 10px;">3 to 6 months</label>
                            </div>
                            <div class="form-check mb-3" style="border: 1px solid lightgrey; padding: 10px; border-radius: 5px; display: flex; align-items: center;">
                                <input type="radio" name="option" value="1 to 3 months" class="form-check-input" id="option3">
                                <label class="form-check-label" for="option3" style="margin-left: 10px;">1 to 3 months</label>
                            </div>
                            <div class="form-check mb-3" style="border: 1px solid lightgrey; padding: 10px; border-radius: 5px; display: flex; align-items: center;">
                                <input type="radio" name="option" value="1 to 4 weeks" class="form-check-input" id="option4">
                                <label class="form-check-label" for="option4" style="margin-left: 10px;">1 to 4 weeks</label>
                            </div>
                            <div class="form-check mb-3" style="border: 1px solid lightgrey; padding: 10px; border-radius: 5px; display: flex; align-items: center;">
                                <input type="radio" name="option" value="I'm not really sure" class="form-check-input" id="option5">
                                <label class="form-check-label" for="option5" style="margin-left: 10px;">I'm not really sure</label>
                            </div>
                        </div>

                        <!-- Add gap between radio section and buttons -->
                        <div style="margin-top: 20px;">
                            <input type="hidden" name="selected_option" id="selected-option-input" value="">

                            <button type="button" class="btn btn-secondary prev-step" data-prev="1">Back</button>
                            <button type="button" class="btn btn-primary next-step" data-next="3">Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: User Details -->
        <div id="step-3" class="step" style="display:none;">
            <div class="container">
                <div class="row">
                    <!-- Left Section: Text and Image -->
                    <div class="col-md-6 d-flex flex-column justify-content-center text-light bg-dark p-5">
                        <h2>Find your perfect match</h2>
                        <p>Answer 6 short questions to help us understand your needs.</p>
                        <ul class="advantages">
                            <li>
                                <img src="<?php echo STEP_FORM_PLUGIN_URL . 'assets/images/verified-icon.png'; ?>" alt="Verified" class="verified-icon">
                                Gain access to 5000+ experts
                            </li>
                            <li>
                                <img src="<?php echo STEP_FORM_PLUGIN_URL . 'assets/images/verified-icon.png'; ?>" alt="Verified" class="verified-icon">
                                Get matched with a developer in 2 days
                            </li>
                            <li>
                                <img src="<?php echo STEP_FORM_PLUGIN_URL . 'assets/images/verified-icon.png'; ?>" alt="Verified" class="verified-icon">
                                Hire quickly and easily with 94% match success
                            </li>
                        </ul>
                    </div>

                    <!-- Right Section: User Details Form -->
                    <div class="col-md-6 bg-white p-5">
                        <h3 class="mb-3">We'd like to get in touch and hear about your needs</h3>

                        <!-- Name Input -->
                        <div class="mb-3">
                            <label for="user-name" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" id="user-name" required>
                        </div>

                        <!-- Email Input -->
                        <div class="mb-3">
                            <label for="user-email" class="form-label">Your company email</label>
                            <input type="email" name="email" class="form-control" id="user-email" required>
                        </div>

                        <!-- Phone Input -->
                        <div class="mb-3">
                            <label for="user-phone" class="form-label">Phone number</label>
                            <input type="tel" name="phone" class="form-control" id="user-phone" required>
                        </div>

                        <!-- I Agree Checkbox -->
                        <div class="form-check mb-3">
                            <input type="checkbox" name="agree" class="form-check-input" id="user-terms-checkbox" required>
                            <label class="form-check-label" for="user-terms-checkbox">
                                <strong>I agree to let Proxify AB use my personal data to create and offer for their services.</strong>
                                Read our <a href="#">Privacy Policy</a> and <a href="#">Cookie Policy</a>.
                            </label>
                        </div>

                        <!-- Buttons -->
                        <button type="button" class="btn btn-secondary prev-step" data-prev="2" style="text-transform: uppercase;">Back</button>
                        <button type="submit" id="submit-form" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </div>
        </div>


    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('step_form', 'step_form_shortcode');