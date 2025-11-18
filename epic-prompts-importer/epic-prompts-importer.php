<?php
/**
 * Plugin Name: Epic Prompts JSON Importer
 * Plugin URI: https://www.epic-prompts.com
 * Description: Import AI prompts from remote JSON files. Automatically creates prompts, taxonomies, and metadata.
 * Version: 1.0.0
 * Author: Epic Prompts Team
 * Author URI: https://www.epic-prompts.com
 * License: GPL v2 or later
 * Text Domain: epic-prompts-importer
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

class Epic_Prompts_Importer {

    /**
     * Plugin version
     */
    const VERSION = '1.0.0';

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_ajax_epi_import_json', array($this, 'ajax_import_json'));
        add_action('wp_ajax_epi_validate_json', array($this, 'ajax_validate_json'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=ai_prompt',
            __('Import Prompts', 'epic-prompts-importer'),
            __('Import JSON', 'epic-prompts-importer'),
            'manage_options',
            'epic-prompts-importer',
            array($this, 'render_admin_page')
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ('ai_prompt_page_epic-prompts-importer' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'epi-admin-style',
            plugin_dir_url(__FILE__) . 'assets/admin.css',
            array(),
            self::VERSION
        );

        wp_enqueue_script(
            'epi-admin-script',
            plugin_dir_url(__FILE__) . 'assets/admin.js',
            array('jquery'),
            self::VERSION,
            true
        );

        wp_localize_script('epi-admin-script', 'epiAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('epi_import_nonce'),
            'strings' => array(
                'importing' => __('Importing...', 'epic-prompts-importer'),
                'validating' => __('Validating...', 'epic-prompts-importer'),
                'success' => __('Import completed successfully!', 'epic-prompts-importer'),
                'error' => __('Import failed. Check the error messages.', 'epic-prompts-importer'),
            )
        ));
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        ?>
        <div class="wrap epi-admin-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <div class="epi-container">
                <!-- Import Form -->
                <div class="epi-card">
                    <h2><?php _e('Import Prompts from JSON', 'epic-prompts-importer'); ?></h2>
                    <p><?php _e('Import AI prompts from a remote JSON file. The plugin will automatically create prompts, taxonomies, and all necessary metadata.', 'epic-prompts-importer'); ?></p>

                    <form id="epi-import-form" class="epi-form">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="json_url"><?php _e('JSON URL', 'epic-prompts-importer'); ?></label>
                                </th>
                                <td>
                                    <input type="url"
                                           id="json_url"
                                           name="json_url"
                                           class="regular-text"
                                           placeholder="https://example.com/prompts.json"
                                           required>
                                    <p class="description">
                                        <?php _e('Enter the full URL to your JSON file. Must be publicly accessible.', 'epic-prompts-importer'); ?>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="import_mode"><?php _e('Import Mode', 'epic-prompts-importer'); ?></label>
                                </th>
                                <td>
                                    <select id="import_mode" name="import_mode">
                                        <option value="skip"><?php _e('Skip duplicates (default)', 'epic-prompts-importer'); ?></option>
                                        <option value="update"><?php _e('Update existing prompts', 'epic-prompts-importer'); ?></option>
                                        <option value="fresh"><?php _e('Delete all & fresh import', 'epic-prompts-importer'); ?></option>
                                    </select>
                                    <p class="description">
                                        <?php _e('Choose how to handle existing prompts during import.', 'epic-prompts-importer'); ?>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="default_author"><?php _e('Default Author', 'epic-prompts-importer'); ?></label>
                                </th>
                                <td>
                                    <?php
                                    wp_dropdown_users(array(
                                        'id' => 'default_author',
                                        'name' => 'default_author',
                                        'selected' => get_current_user_id(),
                                        'show_option_none' => __('Current user', 'epic-prompts-importer'),
                                    ));
                                    ?>
                                    <p class="description">
                                        <?php _e('User to assign as author for imported prompts.', 'epic-prompts-importer'); ?>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="post_status"><?php _e('Post Status', 'epic-prompts-importer'); ?></label>
                                </th>
                                <td>
                                    <select id="post_status" name="post_status">
                                        <option value="publish"><?php _e('Published', 'epic-prompts-importer'); ?></option>
                                        <option value="draft"><?php _e('Draft', 'epic-prompts-importer'); ?></option>
                                        <option value="pending"><?php _e('Pending Review', 'epic-prompts-importer'); ?></option>
                                    </select>
                                    <p class="description">
                                        <?php _e('Status for imported prompts.', 'epic-prompts-importer'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <p class="submit">
                            <button type="button" id="epi-validate-btn" class="button">
                                <?php _e('Validate JSON', 'epic-prompts-importer'); ?>
                            </button>
                            <button type="submit" id="epi-import-btn" class="button button-primary">
                                <?php _e('Start Import', 'epic-prompts-importer'); ?>
                            </button>
                        </p>
                    </form>

                    <!-- Progress Bar -->
                    <div id="epi-progress" class="epi-progress" style="display: none;">
                        <div class="epi-progress-bar">
                            <div class="epi-progress-fill" style="width: 0%;"></div>
                        </div>
                        <p class="epi-progress-text">0 / 0 prompts imported</p>
                    </div>

                    <!-- Messages -->
                    <div id="epi-messages" class="epi-messages"></div>
                </div>

                <!-- JSON Format Documentation -->
                <div class="epi-card">
                    <h2><?php _e('JSON Format', 'epic-prompts-importer'); ?></h2>
                    <p><?php _e('Your JSON file must follow this structure:', 'epic-prompts-importer'); ?></p>

                    <pre><code>{
  "version": "1.0",
  "total_prompts": 100,
  "prompts": [
    {
      "title": "Prompt Title",
      "prompt_text": "The actual prompt text",
      "description": "Description and usage tips",
      "platform": "ChatGPT",
      "prompt_type": "Text Generation",
      "category": "Content Writing",
      "tags": ["tag1", "tag2"],
      "difficulty": "beginner",
      "estimated_tokens": 150
    }
  ]
}</code></pre>

                    <p>
                        <a href="<?php echo esc_url(plugin_dir_url(__FILE__) . '../PROMPT_GENERATORE_JSON.md'); ?>" target="_blank" class="button">
                            <?php _e('View Full Documentation', 'epic-prompts-importer'); ?>
                        </a>
                        <a href="<?php echo esc_url(plugin_dir_url(__FILE__) . 'example-prompts.json'); ?>" target="_blank" class="button">
                            <?php _e('Download Example JSON', 'epic-prompts-importer'); ?>
                        </a>
                    </p>
                </div>

                <!-- Import History -->
                <div class="epi-card">
                    <h2><?php _e('Recent Imports', 'epic-prompts-importer'); ?></h2>
                    <?php $this->render_import_history(); ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render import history
     */
    private function render_import_history() {
        $history = get_option('epi_import_history', array());

        if (empty($history)) {
            echo '<p>' . __('No imports yet.', 'epic-prompts-importer') . '</p>';
            return;
        }

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th>' . __('Date', 'epic-prompts-importer') . '</th>';
        echo '<th>' . __('URL', 'epic-prompts-importer') . '</th>';
        echo '<th>' . __('Prompts', 'epic-prompts-importer') . '</th>';
        echo '<th>' . __('Status', 'epic-prompts-importer') . '</th>';
        echo '</tr></thead><tbody>';

        foreach (array_reverse(array_slice($history, -10)) as $item) {
            echo '<tr>';
            echo '<td>' . esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $item['timestamp'])) . '</td>';
            echo '<td><code>' . esc_html(substr($item['url'], 0, 50)) . '...</code></td>';
            echo '<td>' . esc_html($item['imported'] . ' / ' . $item['total']) . '</td>';
            echo '<td><span class="epi-status epi-status-' . esc_attr($item['status']) . '">' . esc_html(ucfirst($item['status'])) . '</span></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    }

    /**
     * AJAX: Validate JSON
     */
    public function ajax_validate_json() {
        check_ajax_referer('epi_import_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'epic-prompts-importer')));
        }

        $json_url = isset($_POST['json_url']) ? esc_url_raw($_POST['json_url']) : '';

        if (empty($json_url)) {
            wp_send_json_error(array('message' => __('JSON URL is required.', 'epic-prompts-importer')));
        }

        // Fetch JSON
        $response = wp_remote_get($json_url, array(
            'timeout' => 30,
            'headers' => array(
                'Accept' => 'application/json',
            ),
        ));

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(array('message' => __('Invalid JSON format: ', 'epic-prompts-importer') . json_last_error_msg()));
        }

        // Validate structure
        $validation = $this->validate_json_structure($data);

        if (!$validation['valid']) {
            wp_send_json_error(array('message' => $validation['error']));
        }

        wp_send_json_success(array(
            'message' => __('JSON is valid!', 'epic-prompts-importer'),
            'total_prompts' => count($data['prompts']),
            'version' => isset($data['version']) ? $data['version'] : 'unknown',
        ));
    }

    /**
     * AJAX: Import JSON
     */
    public function ajax_import_json() {
        check_ajax_referer('epi_import_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'epic-prompts-importer')));
        }

        // Increase execution time
        set_time_limit(300); // 5 minutes

        $json_url = isset($_POST['json_url']) ? esc_url_raw($_POST['json_url']) : '';
        $import_mode = isset($_POST['import_mode']) ? sanitize_text_field($_POST['import_mode']) : 'skip';
        $default_author = isset($_POST['default_author']) ? intval($_POST['default_author']) : get_current_user_id();
        $post_status = isset($_POST['post_status']) ? sanitize_text_field($_POST['post_status']) : 'publish';

        if (empty($json_url)) {
            wp_send_json_error(array('message' => __('JSON URL is required.', 'epic-prompts-importer')));
        }

        // Fetch JSON
        $response = wp_remote_get($json_url, array(
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(array('message' => __('Invalid JSON format.', 'epic-prompts-importer')));
        }

        // Validate
        $validation = $this->validate_json_structure($data);
        if (!$validation['valid']) {
            wp_send_json_error(array('message' => $validation['error']));
        }

        // Fresh import mode: delete all existing prompts
        if ($import_mode === 'fresh') {
            $this->delete_all_prompts();
        }

        // Import prompts
        $results = $this->import_prompts($data['prompts'], array(
            'import_mode' => $import_mode,
            'default_author' => $default_author,
            'post_status' => $post_status,
        ));

        // Save to history
        $this->save_import_history($json_url, $results);

        wp_send_json_success($results);
    }

    /**
     * Validate JSON structure
     */
    private function validate_json_structure($data) {
        if (!isset($data['prompts']) || !is_array($data['prompts'])) {
            return array(
                'valid' => false,
                'error' => __('Missing "prompts" array in JSON.', 'epic-prompts-importer'),
            );
        }

        if (empty($data['prompts'])) {
            return array(
                'valid' => false,
                'error' => __('Prompts array is empty.', 'epic-prompts-importer'),
            );
        }

        // Validate first prompt structure
        $first = $data['prompts'][0];
        $required_fields = array('title', 'prompt_text', 'platform', 'prompt_type', 'category');

        foreach ($required_fields as $field) {
            if (!isset($first[$field]) || empty($first[$field])) {
                return array(
                    'valid' => false,
                    'error' => sprintf(__('Missing required field "%s" in prompt data.', 'epic-prompts-importer'), $field),
                );
            }
        }

        return array('valid' => true);
    }

    /**
     * Import prompts
     */
    private function import_prompts($prompts, $options) {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = array();

        foreach ($prompts as $index => $prompt_data) {
            $result = $this->import_single_prompt($prompt_data, $options);

            if ($result['success']) {
                if ($result['action'] === 'imported') {
                    $imported++;
                } elseif ($result['action'] === 'updated') {
                    $updated++;
                } else {
                    $skipped++;
                }
            } else {
                $errors[] = sprintf(
                    __('Prompt #%d "%s": %s', 'epic-prompts-importer'),
                    $index + 1,
                    isset($prompt_data['title']) ? $prompt_data['title'] : 'Unknown',
                    $result['error']
                );
                $skipped++;
            }
        }

        return array(
            'total' => count($prompts),
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
            'success' => ($imported + $updated) > 0,
        );
    }

    /**
     * Import single prompt
     */
    private function import_single_prompt($data, $options) {
        // Check if prompt already exists
        $existing = $this->find_existing_prompt($data['title']);

        if ($existing && $options['import_mode'] === 'skip') {
            return array(
                'success' => true,
                'action' => 'skipped',
                'prompt_id' => $existing->ID,
            );
        }

        // Prepare post data
        $post_data = array(
            'post_title' => sanitize_text_field($data['title']),
            'post_content' => isset($data['description']) ? wp_kses_post($data['description']) : '',
            'post_type' => 'ai_prompt',
            'post_status' => $options['post_status'],
            'post_author' => $options['default_author'],
        );

        if ($existing && $options['import_mode'] === 'update') {
            $post_data['ID'] = $existing->ID;
            $prompt_id = wp_update_post($post_data);
            $action = 'updated';
        } else {
            $prompt_id = wp_insert_post($post_data);
            $action = 'imported';
        }

        if (is_wp_error($prompt_id)) {
            return array(
                'success' => false,
                'error' => $prompt_id->get_error_message(),
            );
        }

        // Set meta data
        update_post_meta($prompt_id, 'prompt_text', sanitize_textarea_field($data['prompt_text']));

        if (isset($data['difficulty'])) {
            update_post_meta($prompt_id, 'difficulty', sanitize_text_field($data['difficulty']));
        }

        if (isset($data['estimated_tokens'])) {
            update_post_meta($prompt_id, 'estimated_tokens', intval($data['estimated_tokens']));
        }

        if (isset($data['example_output'])) {
            update_post_meta($prompt_id, 'example_output', wp_kses_post($data['example_output']));
        }

        if (isset($data['tips']) && is_array($data['tips'])) {
            update_post_meta($prompt_id, 'tips', $data['tips']);
        }

        if (isset($data['variables']) && is_array($data['variables'])) {
            update_post_meta($prompt_id, 'variables', $data['variables']);
        }

        // Initialize counters
        update_post_meta($prompt_id, 'views_count', 0);
        update_post_meta($prompt_id, 'saves_count', 0);
        update_post_meta($prompt_id, 'copies_count', 0);
        update_post_meta($prompt_id, 'shares_count', 0);
        update_post_meta($prompt_id, 'rating_avg', 0);
        update_post_meta($prompt_id, 'reactions', array(
            'fire' => 0,
            'gem' => 0,
            'creative' => 0,
            'rocket' => 0,
            'mindblown' => 0,
        ));

        // Set taxonomies
        $this->set_taxonomy($prompt_id, 'ai_platform', $data['platform']);
        $this->set_taxonomy($prompt_id, 'prompt_type', $data['prompt_type']);
        $this->set_taxonomy($prompt_id, 'prompt_category', $data['category']);

        if (isset($data['tags']) && is_array($data['tags'])) {
            wp_set_object_terms($prompt_id, $data['tags'], 'prompt_tag');
        }

        return array(
            'success' => true,
            'action' => $action,
            'prompt_id' => $prompt_id,
        );
    }

    /**
     * Find existing prompt by title
     */
    private function find_existing_prompt($title) {
        $existing = get_posts(array(
            'post_type' => 'ai_prompt',
            'title' => $title,
            'posts_per_page' => 1,
            'post_status' => 'any',
        ));

        return !empty($existing) ? $existing[0] : null;
    }

    /**
     * Set taxonomy term
     */
    private function set_taxonomy($post_id, $taxonomy, $term_name) {
        $term = get_term_by('name', $term_name, $taxonomy);

        if (!$term) {
            // Create term if doesn't exist
            $result = wp_insert_term($term_name, $taxonomy);
            if (!is_wp_error($result)) {
                $term_id = $result['term_id'];
            } else {
                return false;
            }
        } else {
            $term_id = $term->term_id;
        }

        wp_set_object_terms($post_id, $term_id, $taxonomy);
        return true;
    }

    /**
     * Delete all prompts
     */
    private function delete_all_prompts() {
        $prompts = get_posts(array(
            'post_type' => 'ai_prompt',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ));

        foreach ($prompts as $prompt) {
            wp_delete_post($prompt->ID, true);
        }
    }

    /**
     * Save import history
     */
    private function save_import_history($url, $results) {
        $history = get_option('epi_import_history', array());

        $history[] = array(
            'timestamp' => time(),
            'url' => $url,
            'total' => $results['total'],
            'imported' => $results['imported'],
            'updated' => $results['updated'],
            'skipped' => $results['skipped'],
            'status' => $results['success'] ? 'success' : 'failed',
        );

        // Keep only last 50 imports
        $history = array_slice($history, -50);

        update_option('epi_import_history', $history);
    }
}

// Initialize plugin
Epic_Prompts_Importer::get_instance();
