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
            array($this, 'settings_section_callback'),
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

    public function settings_section_callback() {
        echo '<p>Enter your GitHub token below. This token will be used to authenticate API requests to GitHub.</p>';
        echo '<p><strong>Instructions:</strong></p>';
        echo '<ol>';
        echo '<li>Go to your GitHub account settings.</li>';
        echo '<li>Navigate to "Developer settings" > "Personal access tokens".</li>';
        echo '<li>Click "Generate new token".</li>';
        echo '<li>Select the necessary scopes for your token (e.g., repo, user).</li>';
        echo '<li>Click "Generate token" and copy the token value. <strong>Note:</strong> You will not be able to see it again once you navigate away from the page.</li>';
        echo '<li>Paste the token in the field below and click "Save Changes".</li>';
        echo '</ol>';
    }

    public function github_token_field_callback() {
        $token = get_option('github_token');
        echo '<input type="text" id="github_token" name="github_token" value="'. esc_attr($token) .'" />';
        echo '<p class="description">Your GitHub token is required to authenticate API requests.</p>';
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
            <hr>
            <h3>Recalling Stored Credentials</h3>
            <p>To recall the stored GitHub credentials in your WordPress application, use the global function <code>get_github_token()</code>. This function retrieves the GitHub token stored in the WordPress database.</p>
            <pre><code class="language-php">// Retrieve the GitHub token
$github_token = get_github_token();

// Use the token to make an API request to GitHub
$response = wp_remote_post('https://api.github.com/repos/your-username/your-repo/issues', array(
    'headers' => array(
        'Authorization' => 'Bearer ' . $github_token,
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'title' => 'Issue Title',
        'body' => 'Issue Body',
        'labels' => array('bug')
    ))
));

if (is_wp_error($response)) {
    error_log('GitHub API request failed: ' . $response->get_error_message());
} else {
    $response_body = wp_remote_retrieve_body($response);
    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code !== 201) {
        error_log('GitHub API request failed: ' . $response_body);
    }
}</code></pre>
        </div>
        <?php
    }
}

// Make the GitHub token globally accessible
function get_github_token() {
    return get_option('github_token');
}
?>