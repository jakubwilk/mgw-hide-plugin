<?php
/**
 * MGW Hide Content Plugin for MyBB 1.8.x
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * Version: 1.0.17
 * MyBB: 1.8.x
 * PHP: 8.1+
 * Description: Advanced content hiding plugin with customizable hide tags and group permissions
 * 
 * Note: Linter errors for MyBB functions are expected when running outside MyBB environment.
 * All MyBB-specific functions and constants will be available when plugin runs in MyBB.
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Plugin information
function mgw_hide_info()
{
    return array(
        "name"          => "MGW Hide Content",
        "description"   => "Advanced content hiding plugin that allows hiding post content from specific user groups using customizable BBCode tags with custom HTML messages.",
        "website"       => "https://jakubwilk.pl",
        "author"        => "Jakub Wilk",
        "authorsite"    => "https://jakubwilk.pl",
        "version"       => "1.0.17",
        "guid"          => "5a8f2c3d9e1b7c4a6f8e2d1a9c5b7e3f",
        "codename"      => "mgw_hide",
        "compatibility" => "18*"
    );
}

// Installation function
function mgw_hide_install()
{
    global $db, $cache;
    
    // Create settings table
    $db->write_query("CREATE TABLE IF NOT EXISTS ".$db->table_prefix."mgw_hide_tags (
        id int(10) unsigned NOT NULL AUTO_INCREMENT,
        tag_name varchar(50) NOT NULL,
        tag_description varchar(255) NOT NULL DEFAULT '',
        allowed_groups text NOT NULL,
        custom_message text NOT NULL DEFAULT '',
        is_active tinyint(1) NOT NULL DEFAULT 1,
        created_at int(10) unsigned NOT NULL,
        updated_at int(10) unsigned NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY tag_name (tag_name)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8");
    
    // Insert default hide tag
    $db->insert_query("mgw_hide_tags", array(
        'tag_name' => 'hide',
        'tag_description' => 'Default hide tag - content visible only to post author, administrators and super moderators',
        'allowed_groups' => '3,4', // Administrators and Super Moderators by default
        'custom_message' => '',
        'is_active' => 1,
        'created_at' => time(),
        'updated_at' => time()
    ));
    
    // Create template group
    $templategroup = array(
        'prefix' => 'mgw_hide',
        'title' => 'MGW Hide Content',
        'isdefault' => 0
    );
    $db->insert_query("templategroups", $templategroup);
    
    // Create templates
    $templates = array(
        array(
            'title' => 'mgw_hide_message',
            'template' => '<div class="mgw_hide_message">
    <div class="mgw_hide_icon">🔒</div>
    <div class="mgw_hide_content">
        <strong>{$lang->mgw_hide_content_hidden}</strong>
        <p>{$message}</p>
        {$additional_info}
    </div>
</div>',
            'sid' => -1,
            'version' => '1.0',
            'dateline' => time()
        ),
        array(
            'title' => 'mgw_hide_message_guest',
            'template' => '<div class="mgw_hide_message mgw_hide_guest">
    <div class="mgw_hide_icon">🔒</div>
    <div class="mgw_hide_content">
        <strong>{$lang->mgw_hide_content_hidden}</strong>
        <p>{$message}</p>
        <p class="mgw_hide_login_prompt">{$lang->mgw_hide_login_required}</p>
    </div>
</div>',
            'sid' => -1,
            'version' => '1.0',
            'dateline' => time()
        ),
        array(
            'title' => 'mgw_hide_visible_content',
            'template' => '<div class="mgw_hide_visible">
    {$content}
</div>',
            'sid' => -1,
            'version' => '1.0',
            'dateline' => time()
        )
    );
    
    foreach($templates as $template)
    {
        $db->insert_query("templates", $template);
    }
    
    // Create settings group
    $setting_group = array(
        'name' => 'mgw_hide',
        'title' => 'MGW Hide Content Settings',
        'description' => 'Settings for MGW Hide Content plugin',
        'disporder' => 5,
        'isdefault' => 0
    );
    
    $gid = $db->insert_query("settinggroups", $setting_group);
    
    // Plugin settings
    $settings = array(
        array(
            'name' => 'mgw_hide_enabled',
            'title' => 'Enable MGW Hide Content',
            'description' => 'Enable or disable the MGW Hide Content plugin functionality.',
            'optionscode' => 'yesno',
            'value' => '1',
            'disporder' => 1,
            'gid' => $gid
        ),
        array(
            'name' => 'mgw_hide_show_message',
            'title' => 'Show Hidden Content Message',
            'description' => 'Message to display when content is hidden from users.',
            'optionscode' => 'textarea',
            'value' => 'This content is hidden. You do not have permission to view it.',
            'disporder' => 2,
            'gid' => $gid
        ),
        array(
            'name' => 'mgw_hide_author_always_see',
            'title' => 'Author Always Sees Content',
            'description' => 'Should the post author always be able to see hidden content?',
            'optionscode' => 'yesno',
            'value' => '1',
            'disporder' => 3,
            'gid' => $gid
        )
    );
    
    foreach($settings as $setting)
    {
        $db->insert_query("settings", $setting);
    }
    
    // Note: ACP module access is handled automatically by MyBB
    // when the admin/modules/config/mgw_hide.php file exists
    
    if(function_exists('rebuild_settings'))
    {
        rebuild_settings();
    }
    $cache->update_usergroups();
}

// Check if plugin is installed
function mgw_hide_is_installed()
{
    global $db;
    return $db->table_exists("mgw_hide_tags");
}

// Uninstall function
function mgw_hide_uninstall()
{
    global $db, $cache;
    
    // Remove database table
    $db->drop_table("mgw_hide_tags");
    
    // Remove settings
    $db->delete_query("settings", "name LIKE 'mgw_hide_%'");
    $db->delete_query("settinggroups", "name = 'mgw_hide'");
    
    // Remove templates
    $db->delete_query("templates", "title LIKE 'mgw_hide_%'");
    $db->delete_query("templategroups", "prefix = 'mgw_hide'");
    
    if(function_exists('rebuild_settings'))
    {
        rebuild_settings();
    }
    $cache->update_usergroups();
}

// Activation function
function mgw_hide_activate()
{
    global $db, $cache;
    
    // Add admin permissions for mgw_hide module
    mgw_hide_add_admin_permissions();
    
    // Clean up any existing problematic MyCode entries first
    mgw_hide_cleanup_mycodes();
    
    // DO NOT ADD MyCode entries - we handle everything with hooks
    // mgw_hide_update_mycodes(); // REMOVED
    
    // Add navigation menu entry
    mgw_hide_add_navigation();
    
    // Update cache
    $cache->update_usergroups();
}

// Deactivation function
function mgw_hide_deactivate()
{
    global $db, $cache;
    
    // Remove MyCode entries
    $db->delete_query("mycode", "title LIKE 'MGW Hide%'");
    
    // Remove navigation menu entry
    mgw_hide_remove_navigation();
    
    // Update cache
    $cache->update_usergroups();
}

// Hook into MyBB - use parse_message_start instead of postbit
$plugins->add_hook("parse_message_start", "mgw_hide_parse_message_start");
$plugins->add_hook("search_results_post", "mgw_hide_search_results");
$plugins->add_hook("admin_config_menu", "mgw_hide_admin_config_menu");

// Main parsing function for parse_message_start hook
function mgw_hide_parse_message_start($message)
{
    global $mybb;
    
    if(!isset($mybb->settings['mgw_hide_enabled']) || $mybb->settings['mgw_hide_enabled'] != 1)
    {
        return $message;
    }
    
    // Get all hide tags from database
    $hide_tags = mgw_hide_get_tags();
    
    foreach($hide_tags as $tag)
    {
        if(!$tag['is_active']) continue;
        
        $pattern = '/\[' . preg_quote($tag['tag_name'], '/') . '\](.*?)\[\/' . preg_quote($tag['tag_name'], '/') . '\]/is';
        
        if(preg_match_all($pattern, $message, $matches, PREG_SET_ORDER))
        {
            foreach($matches as $match)
            {
                $hidden_content = $match[1];
                $full_match = $match[0];
                
                // Simple permission check without relying on $post
                if(mgw_hide_user_can_see($tag))
                {
                    // User can see content - just show the content without tags
                    $replacement = $hidden_content;
                }
                else
                {
                    // User cannot see content - show custom message or default
                    $custom_message = trim($tag['custom_message']);
                    
                    if(!empty($custom_message))
                    {
                        // Use custom HTML message for this tag
                        $replacement = $custom_message;
                    }
                    else
                    {
                        // Fall back to global setting
                        $hide_message = isset($mybb->settings['mgw_hide_show_message']) ? $mybb->settings['mgw_hide_show_message'] : 'This content is hidden.';
                        
                        if(!$mybb->user['uid'])
                        {
                            $replacement = '[🔒 Hidden Content - Please login to view]';
                        }
                        else
                        {
                            $replacement = '[🔒 Hidden Content - ' . htmlspecialchars($hide_message) . ']';
                        }
                    }
                }
                
                $message = str_replace($full_match, $replacement, $message);
            }
        }
    }
    
    return $message;
}

// Simplified permission check without post dependency
function mgw_hide_user_can_see($tag)
{
    global $mybb;
    
    // Guest users cannot see hidden content
    if(!$mybb->user['uid'])
    {
        return false;
    }
    
    // Check if user's group is in allowed groups
    $allowed_groups = explode(',', $tag['allowed_groups']);
    $user_groups = explode(',', $mybb->user['additionalgroups']);
    array_unshift($user_groups, $mybb->user['usergroup']);
    
    foreach($user_groups as $gid)
    {
        if(in_array(trim($gid), $allowed_groups))
        {
            return true;
        }
    }
    
    return false;
}

// Get all hide tags from database
function mgw_hide_get_tags()
{
    global $db;
    static $tags = null;
    
    if($tags === null)
    {
        $tags = array();
        $query = $db->simple_select("mgw_hide_tags", "*", "is_active = 1", array("order_by" => "tag_name"));
        
        while($tag = $db->fetch_array($query))
        {
            $tags[] = $tag;
        }
    }
    
    return $tags;
}

// Get template content
function mgw_hide_get_template($template_name)
{
    global $db, $templates;
    
    // Try to get from MyBB templates cache first
    if(isset($templates) && is_object($templates) && method_exists($templates, 'get'))
    {
        $cached_template = $templates->get($template_name);
        if($cached_template !== false)
        {
            return "\$" . $template_name . " = \"" . addslashes($cached_template) . "\";";
        }
    }
    
    // Get from database
    $query = $db->simple_select("templates", "template", "title = '" . $db->escape_string($template_name) . "'");
    $template = $db->fetch_field($query, "template");
    
    if($template)
    {
        return "\$" . $template_name . " = \"" . addslashes($template) . "\";";
    }
    
    // Fallback to default template
    switch($template_name)
    {
        case 'mgw_hide_message':
            return "\$mgw_hide_message = \"<div class='mgw_hide_message'><div class='mgw_hide_icon'>🔒</div><div class='mgw_hide_content'><strong>Hidden Content</strong><p>{\$hide_message}</p>{\$additional_info}</div></div>\";";
        case 'mgw_hide_message_guest':
            return "\$mgw_hide_message_guest = \"<div class='mgw_hide_message mgw_hide_guest'><div class='mgw_hide_icon'>🔒</div><div class='mgw_hide_content'><strong>Hidden Content</strong><p>{\$hide_message}</p><p class='mgw_hide_login_prompt'>Please login to view this content.</p></div></div>\";";
        case 'mgw_hide_visible_content':
            return "\$mgw_hide_visible_content = \"<div class='mgw_hide_visible'>{\$content}</div>\";";
        default:
            return "\$" . $template_name . " = \"\";";
    }
}

// Handle search results
function mgw_hide_search_results($post)
{
    global $mybb;
    
    if(!isset($mybb->settings['mgw_hide_enabled']) || $mybb->settings['mgw_hide_enabled'] != 1)
    {
        return $post;
    }
    
    // Remove hidden content from search results
    $hide_tags = mgw_hide_get_tags();
    
    foreach($hide_tags as $tag)
    {
        if(!$tag['is_active']) continue;
        
        $pattern = '/\[' . preg_quote($tag['tag_name'], '/') . '\](.*?)\[\/' . preg_quote($tag['tag_name'], '/') . '\]/is';
        
        if(!mgw_hide_user_can_see($tag))
        {
            $post['message'] = preg_replace($pattern, '[Hidden Content]', $post['message']);
        }
    }
    
    return $post;
}

// Add admin permissions for mgw_hide module
function mgw_hide_add_admin_permissions()
{
    global $db;
    
    // Get all administrators (usergroup 4)
    $query = $db->simple_select("users", "uid", "usergroup = 4");
    
    while($admin = $db->fetch_array($query))
    {
        // Check if admin has permissions record
        $admin_opts = $db->fetch_array($db->simple_select("adminoptions", "*", "uid = '" . intval($admin['uid']) . "'"));
        
        if($admin_opts)
        {
            // Update existing permissions
            $permissions = array();
            if($admin_opts['permissions'])
            {
                $permissions = unserialize($admin_opts['permissions']);
            }
            
            // Add config/mgw_hide permission
            if(!isset($permissions['config']))
            {
                $permissions['config'] = array();
            }
            $permissions['config']['mgw_hide'] = 1;
            
            // Update database
            $db->update_query("adminoptions", array(
                'permissions' => $db->escape_string(serialize($permissions))
            ), "uid = '" . intval($admin['uid']) . "'");
        }
        else
        {
            // Create new admin options record
            $permissions = array(
                'config' => array(
                    'mgw_hide' => 1
                )
            );
            
            $db->insert_query("adminoptions", array(
                'uid' => intval($admin['uid']),
                'cpstyle' => '',
                'cplanguage' => 'english',
                'permissions' => $db->escape_string(serialize($permissions))
            ));
        }
    }
}

// Add navigation menu entry
function mgw_hide_add_navigation()
{
    global $db;
    
    // Check if navigation entry already exists
    $existing = $db->fetch_field($db->simple_select("adminsessions", "sid", "data LIKE '%mgw_hide_panel%'", array("limit" => 1)), "sid");
    
    // For MyBB 1.8.x, we use a different approach - modify admin template directly
    // This will be handled by including the menu file in the admin panel
}

// Remove navigation menu entry
function mgw_hide_remove_navigation()
{
    // Navigation cleanup is handled automatically when plugin is deactivated
    // The menu file won't be included anymore
}

// Add menu item to admin configuration menu
function mgw_hide_admin_config_menu($sub_menu)
{
    global $mybb, $lang;
    
    // Only show for administrators and super moderators
    if($mybb->user['usergroup'] == 4 || $mybb->user['usergroup'] == 3)
    {
        $sub_menu[] = array(
            'id' => 'mgw_hide',
            'title' => '🔒 MGW Hide Content',
            'link' => 'mgw_hide_panel.php'
        );
    }
    
    return $sub_menu;
}

// Clean up problematic MyCode entries
function mgw_hide_cleanup_mycodes()
{
    global $db;
    
    // Remove ALL potentially problematic MyCode entries related to hide functionality
    $conditions = array(
        "title LIKE '%hide%'",
        "title LIKE '%MGW Hide%'",
        "replacement LIKE '%HANDLED BY MGW HIDE PLUGIN%'",
        "regex LIKE '%hide%'"
    );
    
    foreach($conditions as $condition)
    {
        $db->delete_query("mycode", $condition);
    }
}