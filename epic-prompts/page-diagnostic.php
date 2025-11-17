<?php
/**
 * Template Name: Diagnostic Full
 * Full diagnostic page to troubleshoot Epic Prompts
 */

// Force display errors for this page only
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

get_header();
?>

<div class="container" style="padding: 2rem 0; max-width: 1200px; margin: 0 auto;">
    <h1 style="color: #6366F1; margin-bottom: 2rem;">🔍 Epic Prompts Diagnostic</h1>

    <!-- WordPress Core -->
    <div style="background: white; padding: 2rem; margin-bottom: 1rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0;">WordPress Core</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #E5E7EB;">
                <td style="padding: 0.5rem; font-weight: 600;">WordPress Version</td>
                <td style="padding: 0.5rem;"><?php echo get_bloginfo('version'); ?></td>
            </tr>
            <tr style="border-bottom: 1px solid #E5E7EB;">
                <td style="padding: 0.5rem; font-weight: 600;">Site URL</td>
                <td style="padding: 0.5rem;"><?php echo home_url(); ?></td>
            </tr>
            <tr style="border-bottom: 1px solid #E5E7EB;">
                <td style="padding: 0.5rem; font-weight: 600;">Active Theme</td>
                <td style="padding: 0.5rem;"><?php echo wp_get_theme()->get('Name'); ?> v<?php echo wp_get_theme()->get('Version'); ?></td>
            </tr>
            <tr style="border-bottom: 1px solid #E5E7EB;">
                <td style="padding: 0.5rem; font-weight: 600;">Theme Directory</td>
                <td style="padding: 0.5rem; font-size: 0.875rem;"><?php echo get_template_directory(); ?></td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">WP_DEBUG</td>
                <td style="padding: 0.5rem;">
                    <?php echo (defined('WP_DEBUG') && WP_DEBUG) ? '✅ Enabled' : '❌ Disabled'; ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- Plugin Status -->
    <div style="background: white; padding: 2rem; margin-bottom: 1rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0;">Epic Prompts Core Plugin</h2>
        <?php
        $plugin_file = 'epic-prompts-core/epic-prompts-core.php';
        $is_plugin_active = is_plugin_active($plugin_file);
        ?>
        <p style="font-size: 1.25rem; margin: 1rem 0;">
            Status:
            <?php if ($is_plugin_active) : ?>
                <span style="color: #10B981; font-weight: 700;">✅ ACTIVE</span>
            <?php else : ?>
                <span style="color: #EF4444; font-weight: 700;">❌ NOT ACTIVE</span>
            <?php endif; ?>
        </p>

        <h3 style="margin-top: 2rem;">Plugin Classes Loaded:</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <?php
            $classes = array(
                'Epic_Prompts_Core',
                'EP_Post_Types',
                'EP_Taxonomies',
                'EP_User_Functions',
                'EP_XP_System',
                'EP_Ajax_Handlers',
                'EP_Admin',
                'EP_REST_API',
            );
            foreach ($classes as $class) :
                $exists = class_exists($class);
            ?>
            <tr style="border-bottom: 1px solid #E5E7EB;">
                <td style="padding: 0.5rem; font-family: monospace;"><?php echo $class; ?></td>
                <td style="padding: 0.5rem;">
                    <?php if ($exists) : ?>
                        <span style="color: #10B981;">✅ Loaded</span>
                    <?php else : ?>
                        <span style="color: #EF4444;">❌ Not Found</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Enqueued Styles -->
    <div style="background: white; padding: 2rem; margin-bottom: 1rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0;">Enqueued CSS Files</h2>
        <?php
        global $wp_styles;
        if (!empty($wp_styles->queue)) :
        ?>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="background: #F9FAFB; border-bottom: 2px solid #E5E7EB;">
                        <th style="padding: 0.75rem; text-align: left;">Handle</th>
                        <th style="padding: 0.75rem; text-align: left;">Source</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($wp_styles->queue as $handle) :
                    if (isset($wp_styles->registered[$handle])) :
                        $style = $wp_styles->registered[$handle];
                ?>
                    <tr style="border-bottom: 1px solid #E5E7EB;">
                        <td style="padding: 0.75rem; font-family: monospace;"><?php echo $handle; ?></td>
                        <td style="padding: 0.75rem; word-break: break-all;"><?php echo $style->src; ?></td>
                    </tr>
                <?php
                    endif;
                endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p style="color: #EF4444;">❌ No CSS files enqueued!</p>
        <?php endif; ?>
    </div>

    <!-- Enqueued Scripts -->
    <div style="background: white; padding: 2rem; margin-bottom: 1rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0;">Enqueued JavaScript Files</h2>
        <?php
        global $wp_scripts;
        if (!empty($wp_scripts->queue)) :
        ?>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="background: #F9FAFB; border-bottom: 2px solid #E5E7EB;">
                        <th style="padding: 0.75rem; text-align: left;">Handle</th>
                        <th style="padding: 0.75rem; text-align: left;">Source</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($wp_scripts->queue as $handle) :
                    if (isset($wp_scripts->registered[$handle])) :
                        $script = $wp_scripts->registered[$handle];
                ?>
                    <tr style="border-bottom: 1px solid #E5E7EB;">
                        <td style="padding: 0.75rem; font-family: monospace;"><?php echo $handle; ?></td>
                        <td style="padding: 0.75rem; word-break: break-all;"><?php echo $script->src; ?></td>
                    </tr>
                <?php
                    endif;
                endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p style="color: #EF4444;">❌ No JavaScript files enqueued!</p>
        <?php endif; ?>
    </div>

    <!-- File Existence Check -->
    <div style="background: white; padding: 2rem; margin-bottom: 1rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0;">Theme Files Check</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <?php
            $files_to_check = array(
                'style.css' => get_template_directory() . '/style.css',
                'css/custom.css' => get_template_directory() . '/css/custom.css',
                'css/professional.css' => get_template_directory() . '/css/professional.css',
                'js/main.js' => get_template_directory() . '/js/main.js',
                'includes/widgets.php' => get_template_directory() . '/includes/widgets.php',
                'includes/shortcodes.php' => get_template_directory() . '/includes/shortcodes.php',
                'includes/customizer.php' => get_template_directory() . '/includes/customizer.php',
                'includes/onboarding.php' => get_template_directory() . '/includes/onboarding.php',
            );
            foreach ($files_to_check as $name => $path) :
                $exists = file_exists($path);
                $readable = is_readable($path);
            ?>
            <tr style="border-bottom: 1px solid #E5E7EB;">
                <td style="padding: 0.5rem; font-family: monospace;"><?php echo $name; ?></td>
                <td style="padding: 0.5rem;">
                    <?php if ($exists && $readable) : ?>
                        <span style="color: #10B981;">✅ Exists & Readable</span>
                    <?php elseif ($exists) : ?>
                        <span style="color: #F59E0B;">⚠️ Exists but NOT Readable</span>
                    <?php else : ?>
                        <span style="color: #EF4444;">❌ Does NOT Exist</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- PHP Errors -->
    <div style="background: white; padding: 2rem; margin-bottom: 1rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0;">Recent PHP Errors</h2>
        <?php
        $error_log = WP_CONTENT_DIR . '/debug.log';
        if (file_exists($error_log) && is_readable($error_log)) :
            $errors = file_get_contents($error_log);
            $recent_errors = array_slice(explode("\n", $errors), -20);
            if (!empty(trim(implode('', $recent_errors)))) :
        ?>
            <pre style="background: #1F2937; color: #F9FAFB; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; font-size: 0.75rem;"><?php echo esc_html(implode("\n", $recent_errors)); ?></pre>
        <?php else : ?>
            <p style="color: #10B981;">✅ No recent errors in debug.log</p>
        <?php
            endif;
        else :
        ?>
            <p style="color: #6B7280;">ℹ️ Debug log file not found or not readable</p>
            <p style="font-size: 0.875rem;">Enable WP_DEBUG in wp-config.php to see errors</p>
        <?php endif; ?>
    </div>

    <!-- Actions -->
    <div style="background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%); padding: 2rem; border-radius: 0.5rem; color: white; text-align: center;">
        <h2 style="margin-top: 0;">Next Steps</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1.5rem;">
            <a href="<?php echo admin_url('plugins.php'); ?>" style="display: block; padding: 1rem; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600;">
                📦 Manage Plugins
            </a>
            <a href="<?php echo admin_url('themes.php'); ?>" style="display: block; padding: 1rem; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600;">
                🎨 Manage Themes
            </a>
            <a href="<?php echo admin_url('options-permalink.php'); ?>" style="display: block; padding: 1rem; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600;">
                🔗 Fix Permalinks
            </a>
            <a href="<?php echo home_url(); ?>" style="display: block; padding: 1rem; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600;">
                🏠 Go to Homepage
            </a>
        </div>
    </div>
</div>

<?php get_footer(); ?>
