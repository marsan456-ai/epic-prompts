<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Epic Prompts - Test</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #F9FAFB;
            color: #111827;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        .header {
            background: white;
            padding: 1rem 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .header h1 {
            color: #6366F1;
            font-size: 1.5rem;
        }
        .header span {
            color: #EC4899;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #6366F1;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 600;
            margin: 0.5rem;
        }
        .btn:hover {
            background: #4F46E5;
        }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        .status {
            padding: 1rem;
            background: #D1FAE5;
            color: #065F46;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid #10B981;
        }
        .error {
            background: #FEE2E2;
            color: #991B1B;
            border-left-color: #EF4444;
        }
        ul {
            padding-left: 2rem;
        }
        li {
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Epic<span>Prompts</span></h1>
        </div>
    </div>

    <div class="container">
        <div class="status">
            ✅ <strong>Basic HTML & CSS Working!</strong> This page is loading correctly.
        </div>

        <div class="card">
            <h2 style="margin-bottom: 1rem;">WordPress Installation Check</h2>

            <?php if (defined('ABSPATH')) : ?>
                <div class="status">
                    ✅ WordPress is installed and loading
                </div>

                <p><strong>WordPress Version:</strong> <?php echo get_bloginfo('version'); ?></p>
                <p><strong>Site URL:</strong> <?php echo home_url(); ?></p>
                <p><strong>Theme Directory:</strong> <?php echo get_template_directory(); ?></p>

                <?php
                // Check if plugin is active
                $plugin_file = 'epic-prompts-core/epic-prompts-core.php';
                $is_plugin_active = is_plugin_active($plugin_file);
                ?>

                <p><strong>Plugin Status:</strong>
                    <?php if ($is_plugin_active) : ?>
                        <span style="color: #10B981;">✅ Active</span>
                    <?php else : ?>
                        <span style="color: #EF4444;">❌ Not Active</span>
                    <?php endif; ?>
                </p>

                <p><strong>Classes Available:</strong></p>
                <ul>
                    <li>EP_User_Functions: <?php echo class_exists('EP_User_Functions') ? '✅ Yes' : '❌ No'; ?></li>
                    <li>EP_XP_System: <?php echo class_exists('EP_XP_System') ? '✅ Yes' : '❌ No'; ?></li>
                    <li>EP_Post_Types: <?php echo class_exists('EP_Post_Types') ? '✅ Yes' : '❌ No'; ?></li>
                </ul>

            <?php else : ?>
                <div class="status error">
                    ❌ WordPress is NOT loading properly!
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2 style="margin-bottom: 1rem;">Next Steps</h2>
            <ol>
                <li>Make sure WordPress is installed</li>
                <li>Make sure the Epic Prompts Core plugin is activated</li>
                <li>Go to Settings → Permalinks and click "Save Changes"</li>
                <li>Clear browser cache (Ctrl+Shift+R or Cmd+Shift+R)</li>
                <li>If still not working, check WordPress debug.log file</li>
            </ol>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="<?php echo home_url(); ?>" class="btn">Go to Homepage</a>
            <a href="<?php echo admin_url(); ?>" class="btn" style="background: #EC4899;">Go to Admin</a>
        </div>
    </div>
</body>
</html>
