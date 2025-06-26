<?php
/**
 * MGW Hide Content - Hooks Debug
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * Debug which hooks are being called and when
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";
require_once "./inc/functions.php";

if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied.");
}

echo "<h1>🔍 MGW Hide - Hooks Debug</h1>";

// Include plugin functions
if(file_exists("../inc/plugins/mgw_hide.php"))
{
    include_once "../inc/plugins/mgw_hide.php";
}

echo "<h2>📊 Plugin Status</h2>";
echo "<ul>";
echo "<li><strong>Plugin Enabled:</strong> " . (isset($mybb->settings['mgw_hide_enabled']) ? ($mybb->settings['mgw_hide_enabled'] ? 'Yes' : 'No') : 'Not set') . "</li>";
echo "<li><strong>mgw_hide_parse_message_start exists:</strong> " . (function_exists('mgw_hide_parse_message_start') ? 'Yes' : 'No') . "</li>";
echo "<li><strong>mgw_hide_search_results exists:</strong> " . (function_exists('mgw_hide_search_results') ? 'Yes' : 'No') . "</li>";
echo "<li><strong>mgw_hide_search_results_postbit exists:</strong> " . (function_exists('mgw_hide_search_results_postbit') ? 'Yes' : 'No') . "</li>";
echo "</ul>";

// Check what hooks are registered
echo "<h2>🎣 Registered Hooks</h2>";
if(isset($plugins) && isset($plugins->hooks))
{
    $mgw_hooks = array();
    
    // Debug: show the structure of hooks
    echo "<h3>Hook Structure Debug:</h3>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 3px; max-height: 200px; overflow-y: auto;'>";
    
    foreach($plugins->hooks as $hook_name => $hook_data)
    {
        if(strpos($hook_name, 'parse_message') !== false || strpos($hook_name, 'search') !== false || strpos($hook_name, 'postbit') !== false)
        {
            echo "Hook: " . htmlspecialchars($hook_name) . "\n";
            echo "Data type: " . gettype($hook_data) . "\n";
            if(is_array($hook_data))
            {
                foreach($hook_data as $priority => $functions)
                {
                    echo "  Priority: " . htmlspecialchars($priority) . " (" . gettype($functions) . ")\n";
                    if(is_array($functions))
                    {
                        foreach($functions as $key => $function)
                        {
                            echo "    Function: " . htmlspecialchars(print_r($function, true)) . "\n";
                        }
                    }
                    else
                    {
                        echo "    Functions: " . htmlspecialchars($functions) . "\n";
                    }
                }
            }
            echo "\n";
        }
    }
    echo "</pre>";
    
    // Try to find MGW Hide hooks with safer approach
    foreach($plugins->hooks as $hook_name => $hook_data)
    {
        if(is_array($hook_data))
        {
            foreach($hook_data as $priority => $functions)
            {
                if(is_array($functions))
                {
                    foreach($functions as $function)
                    {
                        if(is_string($function) && strpos($function, 'mgw_hide') !== false)
                        {
                            $mgw_hooks[$hook_name][] = $function;
                        }
                        elseif(is_array($function))
                        {
                            // Function might be an array with callback info
                            $func_string = print_r($function, true);
                            if(strpos($func_string, 'mgw_hide') !== false)
                            {
                                $mgw_hooks[$hook_name][] = $func_string;
                            }
                        }
                    }
                }
                elseif(is_string($functions) && strpos($functions, 'mgw_hide') !== false)
                {
                    $mgw_hooks[$hook_name][] = $functions;
                }
            }
        }
    }
    
    echo "<h3>MGW Hide Hooks Found:</h3>";
    if(empty($mgw_hooks))
    {
        echo "<p>❌ No MGW Hide hooks found in plugins system!</p>";
    }
    else
    {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f8f9fa;'><th>Hook Name</th><th>Functions</th></tr>";
        foreach($mgw_hooks as $hook => $functions)
        {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($hook) . "</td>";
            echo "<td>" . implode('<br>', array_map('htmlspecialchars', $functions)) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}
else
{
    echo "<p>❌ Plugins system not available!</p>";
}

// Test message parsing directly
echo "<h2>🧪 Direct Message Parsing Test</h2>";
$test_message = "Public content here. [hide]This should be hidden[/hide] More public content.";
echo "<p><strong>Test Message:</strong> " . htmlspecialchars($test_message) . "</p>";

if(function_exists('mgw_hide_parse_message_start'))
{
    $parsed = mgw_hide_parse_message_start($test_message);
    echo "<p><strong>After Parsing:</strong> " . htmlspecialchars($parsed) . "</p>";
    
    if($parsed == $test_message)
    {
        echo "<p>⚠️ <strong>Warning:</strong> Message unchanged - check if plugin is enabled and user has correct permissions!</p>";
    }
    else
    {
        echo "<p>✅ <strong>Success:</strong> Message was processed!</p>";
    }
}
else
{
    echo "<p>❌ mgw_hide_parse_message_start function not found!</p>";
}

// Check current user permissions for hide tag
echo "<h2>👤 User Permissions Check</h2>";
if(function_exists('mgw_hide_get_tags') && function_exists('mgw_hide_user_can_see'))
{
    $tags = mgw_hide_get_tags();
    if(!empty($tags))
    {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f8f9fa;'><th>Tag</th><th>Allowed Groups</th><th>Can Current User See?</th></tr>";
        foreach($tags as $tag)
        {
            $can_see = mgw_hide_user_can_see($tag);
            echo "<tr>";
            echo "<td>[" . htmlspecialchars($tag['tag_name']) . "]</td>";
            echo "<td>" . htmlspecialchars($tag['allowed_groups']) . "</td>";
            echo "<td style='color: " . ($can_see ? 'green' : 'red') . ";'>" . ($can_see ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    else
    {
        echo "<p>❌ No hide tags found!</p>";
    }
}

// Manual search simulation
echo "<h2>🔍 Manual Search Simulation</h2>";
echo "<p>Simulating what happens when search.php processes a post with [hide] tags:</p>";

// Get a real post with hide tag
$query = $db->simple_select("posts", "pid, message, subject", "message LIKE '%[hide]%'", array("limit" => 1));
if($db->num_rows($query) > 0)
{
    $real_post = $db->fetch_array($query);
    
    echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<h4>Real Post (ID: " . $real_post['pid'] . ")</h4>";
    echo "<p><strong>Subject:</strong> " . htmlspecialchars($real_post['subject']) . "</p>";
    echo "<p><strong>Original:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px;'>" . htmlspecialchars($real_post['message']) . "</pre>";
    
    // Test all our search functions
    $test_post_array = array(
        'pid' => $real_post['pid'],
        'message' => $real_post['message'],
        'subject' => $real_post['subject']
    );
    
    echo "<p><strong>After mgw_hide_search_results():</strong></p>";
    $result1 = mgw_hide_search_results($test_post_array);
    echo "<pre style='background: #e8f5e8; padding: 10px;'>" . htmlspecialchars($result1['message']) . "</pre>";
    
    echo "<p><strong>After mgw_hide_parse_message_start():</strong></p>";
    $result2 = mgw_hide_parse_message_start($real_post['message']);
    echo "<pre style='background: #e3f2fd; padding: 10px;'>" . htmlspecialchars($result2) . "</pre>";
    
    echo "</div>";
}
else
{
    echo "<p>No posts found with [hide] tags for testing.</p>";
}

// Suggest fixes
// Check if plugin is actually activated
echo "<h2>🔍 Plugin Activation Status</h2>";

// First check if plugins table exists
$table_exists = false;
try {
    $tables_query = $db->query("SHOW TABLES LIKE '" . $db->table_prefix . "plugins'");
    if($db->num_rows($tables_query) > 0)
    {
        $table_exists = true;
    }
} catch(Exception $e) {
    // Table doesn't exist or error checking
}

if($table_exists)
{
    try {
        $query = $db->simple_select("plugins", "active", "name = 'mgw_hide'");
        if($db->num_rows($query) > 0)
        {
            $plugin_data = $db->fetch_array($query);
            echo "<p><strong>Plugin in database:</strong> " . ($plugin_data['active'] ? '✅ Active' : '❌ Inactive') . "</p>";
        }
        else
        {
            echo "<p>❌ Plugin not found in database!</p>";
        }
    } catch(Exception $e) {
        echo "<p>⚠️ Error checking plugin status: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
else
{
    echo "<p>⚠️ Plugins table doesn't exist - this MyBB installation might not use the standard plugins table</p>";
    
    // Alternative check - see if plugin functions exist and hooks are registered
    $plugin_seems_active = false;
    if(function_exists('mgw_hide_parse_message_start') && 
       function_exists('mgw_hide_search_results') && 
       isset($plugins) && 
       isset($plugins->hooks['parse_message_start']))
    {
        $plugin_seems_active = true;
    }
    
    echo "<p><strong>Plugin functions status:</strong> " . ($plugin_seems_active ? '✅ Functions exist and hooks registered' : '❌ Functions missing or hooks not registered') . "</p>";
}

// Check if MyBB is calling hooks in search context
echo "<h2>🎯 Search Context Test</h2>";
if(isset($_GET['simulate_search']))
{
    echo "<p>Simulating search context...</p>";
    $_GET['action'] = 'results'; // Simulate search results page
    
    // Test if our hooks would be called
    if(function_exists('mgw_hide_parse_message_start'))
    {
        $test_msg = "Test message [hide]hidden content[/hide] end.";
        $result = mgw_hide_parse_message_start($test_msg);
        echo "<p><strong>Parse function test:</strong></p>";
        echo "<p>Input: " . htmlspecialchars($test_msg) . "</p>";
        echo "<p>Output: " . htmlspecialchars($result) . "</p>";
        
        if($result != $test_msg)
        {
            echo "<p>✅ Function is working!</p>";
        }
        else
        {
            echo "<p>⚠️ Function didn't modify the message</p>";
        }
    }
}
else
{
    echo "<p><a href='?simulate_search=1' style='background: #2196f3; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px;'>Run Search Simulation</a></p>";
}

echo "<h2>🔧 Suggested Fixes</h2>";
echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px;'>";
echo "<h4>If tags are still visible in search:</h4>";
echo "<ol>";
echo "<li><strong>Check if plugin is activated:</strong> Go to ACP → Configuration → Plugins → MGW Hide Content → Activate</li>";
echo "<li><strong>Clear all caches:</strong> ACP → Tools & Maintenance → Cache Manager → Rebuild All Caches</li>";
echo "<li><strong>Deactivate and reactivate:</strong> This refreshes all hooks</li>";
echo "<li><strong>Use Force Fix:</strong> <a href='mgw_hide_force_fix.php'>JavaScript fix</a> is most reliable</li>";
echo "<li><strong>Check search.php file:</strong> Make sure it calls the hooks properly</li>";
echo "</ol>";
echo "</div>";

echo "<h2>🎯 Quick Actions</h2>";
echo "<p><a href='?clear_cache=1' style='background: #007cba; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px;'>Clear All Caches</a></p>";

if(isset($_GET['clear_cache']))
{
    if($cache)
    {
        $cache->update_mycode();
        $cache->update_settings();
        $cache->update_usergroups();
        $cache->update_forums();
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
        echo "✅ All caches cleared!";
        echo "</div>";
    }
}

echo "<p><a href='mgw_hide_panel.php'>← Back to MGW Hide Panel</a></p>";
?> 