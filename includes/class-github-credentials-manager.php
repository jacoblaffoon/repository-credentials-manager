<?php
if (!defined('ABSPATH')) {
    exit;
}

class GitHub_Credentials_Manager {

    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_admin_menu() {
        add_options_page(
            'GitHub Credentials',
            'GitHub Credentials',
            'manage_options',
            'github-credentials',
            array($this, 'settings_page')
        );
    }

    public function register_settings() {
        register_setting('github_credentials_options', 'github_token');

        add_settings_section(
            'github_credentials_section',
            'GitHub Credentials Settings',
            null,
            'github-credentials'
        );

        add_settings_field(
            'github_token',
            'GitHub Token',
            array($this, 'github_token_field_callback'),
            'github-credentials',
            'github_credentials_section'
        );
    }

    public function github_token_field_callback() {
        $token = get_option('github_token');
        echo '<input type="text" id="github_token" name="github_token" value="'. esc_attr($token) .'" />';
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>GitHub Credentials Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('github_credentials_options');
                do_settings_sections('github-credentials');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

// Make the GitHub token globally accessible
function get_github_token() {
    return get_option('github_token');
}