# GitHub Credentials Manager

A WordPress plugin to securely store GitHub credentials and make them accessible globally within your WordPress application.

## File Structure
github-credentials-manager/
├── github-credentials-manager.php
├── includes/
│ └── class-github-credentials-manager.php

## Installation

1. **Download the Plugin:**
   - Clone or download the plugin files from the repository.

2. **Upload to WordPress:**
   - Upload the `github-credentials-manager` directory to the `/wp-content/plugins/` directory.

3. **Activate the Plugin:**
   - Go to the `Plugins` menu in WordPress and activate the `GitHub Credentials Manager` plugin.

## Usage

### Storing GitHub Credentials

1. **Navigate to Settings:**
   - In the WordPress admin dashboard, go to `Settings` > `GitHub Credentials`.

2. **Enter GitHub Token:**
   - Enter your GitHub personal access token in the provided field and click `Save Changes`.

### Recalling Stored Credentials

To recall the stored GitHub credentials in your WordPress application, use the global function `get_github_token()`. This function retrieves the GitHub token stored in the WordPress database.

#### Example Usage:

```php
// Retrieve the GitHub token
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
}