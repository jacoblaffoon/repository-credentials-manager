<?php
if (!defined('ABSPATH')) {
    exit;
}

class Repository_Credentials_Manager {

    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_footer', array($this, 'admin_footer_scripts'));
    }

    public function add_admin_menu() {
        add_options_page(
            'Repository Credentials',
            'Repository Credentials',
            'manage_options',
            'repository-credentials',
            array($this, 'settings_page')
        );
    }

    public function register_settings() {
        register_setting('repository_credentials_options', 'github_token', array(
            'sanitize_callback' => array($this, 'sanitize_github_token')
        ));

        add_settings_section(
            'repository_credentials_section',
            'Repository Credentials Settings',
            array($this, 'settings_section_callback'),
            'repository-credentials'
        );

        add_settings_field(
            'github_token',
            'GitHub Token',
            array($this, 'github_token_field_callback'),
            'repository-credentials',
            'repository_credentials_section'
        );
    }

    public function sanitize_github_token($input) {
        // Add sanitization logic if necessary
        return sanitize_text_field($input);
    }

    public function settings_section_callback() {
        echo '<p>Enter your GitHub token below. This token will be used to authenticate API requests to GitHub.</p>';
        echo '<p><strong>Instructions:</strong></p>';
        echo '<ol>';
        echo '<li>Go to your GitHub account settings.</li>';
        echo '<li>Navigate to "Developer settings" > "Personal access tokens".</li>';
        echo '<li>Click "Generate new token".</li>';
        echo '<li>Select the necessary scopes for your token (e.g., repo, user).</li>';
        echo '<li>Click "Generate token" and copy the token value. <strong>Note:</strong> You won\'t be able to see it again once you navigate away from the page.</li>';
        echo '<li>Paste the token in the field below and click "Save Changes".</li>';
        echo '</ol>';
    }

    public function github_token_field_callback() {
        $user_id = get_current_user_id();
        $token = get_user_meta($user_id, 'github_token', true);
        echo '<input type="text" id="github_token" name="github_token" value="'. esc_attr($token) .'" style="width: 100%;" />';
        echo '<p class="description">Your GitHub token is required to authenticate API requests.</p>';
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>Repository Credentials Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('repository_credentials_options');
                do_settings_sections('repository-credentials');
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
            <h3>Test Recall</h3>
            <button id="toggle-test-recall" class="button">Toggle Test Recall</button>
            <div id="test-recall" style="display: none;">
                <p>Current GitHub Token Value:</p>
                <pre><?php echo esc_html(get_github_token()); ?></pre>
            </div>
        </div>
        <?php
    }

    public function admin_footer_scripts() {
        ?>
        <script type="text/javascript">
            document.getElementById('toggle-test-recall').addEventListener('click', function() {
                var testRecallDiv = document.getElementById('test-recall');
                if (testRecallDiv.style.display === 'none') {
                    testRecallDiv.style.display = 'block';
                } else {
                    testRecallDiv.style.display = 'none';
                }
            });
        </script>
        <?php
    }
}

// Make the GitHub token globally accessible
function get_github_token() {
    $user_id = get_current_user_id();
    return get_user_meta($user_id, 'github_token', true);
}

// Save GitHub token for the current user
function save_github_token() {
    if (isset($_POST['github_token'])) {
        $user_id = get_current_user_id();
        $token = sanitize_text_field($_POST['github_token']);
        update_user_meta($user_id, 'github_token', $token);
    }
}
add_action('admin_init', 'save_github_token');