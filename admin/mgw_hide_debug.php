<?php
/**
 * MGW Hide Content - Debug Script
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";
require_once "./inc/functions.php";

if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied.");
}

echo "<h1>🔍 MGW Hide Content - Debug Information</h1>";

// 1. Check if plugin is active
echo "<h2>1. Plugin Status</h2>";
$plugin_active = false;
if(function_exists('mgw_hide_info'))
{
    $plugin_info = mgw_hide_info();
    echo "<p>✅ Plugin loaded: " . $plugin_info['name'] . " v" . $plugin_info['version'] . "</p>";
    $plugin_active = true;
}
else
{
    echo "<p>❌ Plugin NOT loaded or activated</p>";
}

// 2. Check MyCode entries
echo "<h2>2. MyCode Entries</h2>";
$query = $db->simple_select("mycode", "*", "", array("order_by" => "cid"));
$mycode_count = 0;
$problematic_count = 0;

while($entry = $db->fetch_array($query))
{
    $mycode_count++;
    $is_problematic = false;
    
    if(stripos($entry['title'], 'hide') !== false || 
       stripos($entry['title'], 'mgw') !== false ||
       stripos($entry['replacement'], 'HANDLED BY MGW HIDE PLUGIN') !== false ||
       stripos($entry['replacement'], 'PLUGIN') !== false)
    {
        $is_problematic = true;
        $problematic_count++;
    }
    
    $color = $is_problematic ? 'style="background: #ffebee; border: 2px solid red; color: red;"' : 'style="background: #e8f5e8; border: 1px solid #ccc;"';
    
    echo "<div $color style='margin: 10px 0; padding: 10px; border-radius: 5px;'>";
    echo "<strong>ID:</strong> " . $entry['cid'] . " | ";
    echo "<strong>Title:</strong> " . htmlspecialchars($entry['title']) . " | ";
    echo "<strong>Active:</strong> " . ($entry['active'] ? '✅' : '❌') . "<br>";
    echo "<strong>Regex:</strong> " . htmlspecialchars($entry['regex']) . "<br>";
    echo "<strong>Replacement:</strong> " . htmlspecialchars(substr($entry['replacement'], 0, 200));
    if(strlen($entry['replacement']) > 200) echo "...";
    echo "</div>";
}

echo "<p><strong>Total MyCode entries:</strong> $mycode_count</p>";
echo "<p><strong>Problematic entries:</strong> $problematic_count</p>";

// 3. Check database table
echo "<h2>3. MGW Hide Tags Table</h2>";
if($db->table_exists("mgw_hide_tags"))
{
    echo "<p>✅ Table exists</p>";
    $query = $db->simple_select("mgw_hide_tags", "*", "", array("order_by" => "tag_name"));
    while($tag = $db->fetch_array($query))
    {
        echo "<div style='background: #f0f8ff; border: 1px solid #0066cc; margin: 5px 0; padding: 10px; border-radius: 3px;'>";
        echo "<strong>Tag:</strong> [" . htmlspecialchars($tag['tag_name']) . "] | ";
        echo "<strong>Active:</strong> " . ($tag['is_active'] ? '✅' : '❌') . " | ";
        echo "<strong>Groups:</strong> " . htmlspecialchars($tag['allowed_groups']) . "<br>";
        echo "<strong>Description:</strong> " . htmlspecialchars($tag['tag_description']);
        echo "</div>";
    }
}
else
{
    echo "<p>❌ Table does NOT exist</p>";
}

// 4. Check plugin settings
echo "<h2>4. Plugin Settings</h2>";
echo "<p><strong>Enabled:</strong> " . (isset($mybb->settings['mgw_hide_enabled']) ? ($mybb->settings['mgw_hide_enabled'] ? '✅' : '❌') : '❓ Not set') . "</p>";
echo "<p><strong>Message:</strong> " . (isset($mybb->settings['mgw_hide_show_message']) ? htmlspecialchars($mybb->settings['mgw_hide_show_message']) : '❓ Not set') . "</p>";
echo "<p><strong>Author sees:</strong> " . (isset($mybb->settings['mgw_hide_author_always_see']) ? ($mybb->settings['mgw_hide_author_always_see'] ? '✅' : '❌') : '❓ Not set') . "</p>";

// 5. Test hooks
echo "<h2>5. Hook Test</h2>";
if($plugin_active)
{
    echo "<p>Testing if parsing functions exist:</p>";
    echo "<p><strong>mgw_hide_parse_message_early:</strong> " . (function_exists('mgw_hide_parse_message_early') ? '✅' : '❌') . "</p>";
    echo "<p><strong>mgw_hide_restore_placeholders:</strong> " . (function_exists('mgw_hide_restore_placeholders') ? '✅' : '❌') . "</p>";
    echo "<p><strong>mgw_hide_get_tags:</strong> " . (function_exists('mgw_hide_get_tags') ? '✅' : '❌') . "</p>";
}

// 6. Actions
echo "<h2>6. Quick Actions</h2>";

if($problematic_count > 0)
{
    echo "<div style='background: #ffebee; border: 2px solid red; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
    echo "<h3>🚨 CRITICAL: Found $problematic_count problematic MyCode entries!</h3>";
    echo "<p>These entries are causing the parsing conflicts.</p>";
    echo "<p><a href='mgw_hide_cleanup.php' style='background: red; color: white; padding: 10px; text-decoration: none; border-radius: 3px;'>🧹 RUN CLEANUP SCRIPT</a></p>";
    echo "</div>";
}

echo "<div style='background: #f0f8ff; border: 1px solid #0066cc; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
echo "<h3>🔧 Manual Actions:</h3>";
echo "<p><a href='?action=delete_problematic'>🗑️ Delete All Problematic MyCode</a></p>";
echo "<p><a href='index.php?module=config-plugins'>🔄 Go to Plugin Management</a></p>";
echo "<p><a href='mgw_hide_panel.php'>⚙️ MGW Hide Panel</a></p>";
echo "</div>";

// Handle manual actions
if(isset($_GET['action']) && $_GET['action'] == 'delete_problematic')
{
    echo "<h2>🗑️ Deleting Problematic MyCode Entries</h2>";
    
    $conditions = array(
        "title LIKE '%hide%'",
        "title LIKE '%mgw%'",
        "replacement LIKE '%HANDLED BY MGW HIDE PLUGIN%'",
        "replacement LIKE '%PLUGIN%'"
    );
    
    $total_deleted = 0;
    foreach($conditions as $condition)
    {
        $deleted = $db->delete_query("mycode", $condition);
        echo "<p>Condition: $condition → Deleted: $deleted</p>";
        $total_deleted += $deleted;
    }
    
    echo "<p><strong>✅ Total deleted: $total_deleted</strong></p>";
    echo "<p><a href='mgw_hide_debug.php'>🔄 Refresh this page</a></p>";
}

echo "<hr>";
echo "<p><strong>Next steps if you have problems:</strong></p>";
echo "<ol>";
echo "<li>Delete all problematic MyCode entries (button above)</li>";
echo "<li>Deactivate plugin: Configuration → Plugins → MGW Hide Content → Deactivate</li>";
echo "<li>Activate plugin: Configuration → Plugins → MGW Hide Content → Activate</li>";
echo "<li>Test with a simple post: [hide]test[/hide]</li>";
echo "</ol>";
?> 