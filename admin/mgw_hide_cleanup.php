<?php
/**
 * MGW Hide Content - Cleanup Script
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * Use this script to clean up problematic MyCode entries that may cause parsing conflicts
 * Run this ONCE after installing/updating the plugin
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";
require_once "./inc/functions.php";

// Check admin permissions
if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied. You must be an administrator to run this cleanup script.");
}

echo "<h2>MGW Hide Content - Cleanup Script</h2>";

// First, show all current MyCode entries for debugging
echo "<h3>Current MyCode Entries:</h3>";
$query = $db->simple_select("mycode", "*", "", array("order_by" => "cid"));
while($entry = $db->fetch_array($query))
{
    echo "<div style='border: 1px solid #ddd; margin: 5px; padding: 10px;'>";
    echo "<strong>ID:</strong> " . $entry['cid'] . " | ";
    echo "<strong>Title:</strong> " . htmlspecialchars($entry['title']) . " | ";
    echo "<strong>Active:</strong> " . ($entry['active'] ? 'Yes' : 'No') . "<br>";
    echo "<strong>Regex:</strong> " . htmlspecialchars($entry['regex']) . "<br>";
    echo "<strong>Replacement:</strong> " . htmlspecialchars(substr($entry['replacement'], 0, 100)) . "...";
    echo "</div>";
}

echo "<hr>";

// Clean up ALL problematic MyCode entries - be very aggressive
$conditions = array(
    "title LIKE 'MGW Hide%'",
    "title LIKE '%hide%'",
    "replacement LIKE '%HANDLED BY MGW HIDE PLUGIN%'",
    "replacement LIKE '%PLUGIN%'",
    "regex LIKE '%hide%'",
    "regex LIKE '%MGW%'"
);

$total_deleted = 0;
foreach($conditions as $condition)
{
    echo "<p>Checking condition: " . htmlspecialchars($condition) . "</p>";
    $deleted = $db->delete_query("mycode", $condition);
    echo "<p>→ Deleted: " . $deleted . " entries</p>";
    $total_deleted += $deleted;
}

echo "<h3>✅ Total Removed: " . $total_deleted . " problematic MyCode entries.</h3>";

// Check for any remaining problematic entries
$query = $db->simple_select("mycode", "COUNT(*) as count", "replacement LIKE '%HANDLED BY MGW HIDE PLUGIN%' OR title LIKE '%MGW Hide%'");
$remaining = $db->fetch_field($query, "count");

if($remaining > 0)
{
    echo "<p>⚠️ Warning: Found " . $remaining . " remaining problematic entries.</p>";
    
    // Show remaining entries
    $query = $db->simple_select("mycode", "*", "replacement LIKE '%HANDLED BY MGW HIDE PLUGIN%' OR title LIKE '%MGW Hide%'");
    echo "<h4>Remaining entries:</h4>";
    while($entry = $db->fetch_array($query))
    {
        echo "<p>ID: " . $entry['cid'] . " - Title: " . htmlspecialchars($entry['title']) . " - Replacement: " . htmlspecialchars(substr($entry['replacement'], 0, 50)) . "...</p>";
    }
}
else
{
    echo "<p>✅ All problematic entries cleaned up successfully!</p>";
}

// Clear MyBB cache
if($cache)
{
    $cache->update_mycode();
    echo "<p>✅ MyBB cache updated.</p>";
}

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>Deactivate and Reactivate Plugin:</strong><br>Go to Configuration → Plugins → MGW Hide Content → Deactivate → Activate</li>";
echo "<li><strong>Test Tags:</strong><br>Create a post with <code>[hide]test content[/hide]</code></li>";
echo "<li><strong>Check Admin Panel:</strong><br><a href='mgw_hide_panel.php'>MGW Hide Content Panel</a></li>";
echo "<li><strong>Clear MyBB Cache:</strong><br>Tools & Maintenance → Cache Manager → Rebuild All Caches</li>";
echo "</ol>";

// Manual cleanup option
if(isset($_GET['delete_all']))
{
    echo "<h3>🔥 NUCLEAR OPTION - Deleting ALL MyCode entries:</h3>";
    $deleted_all = $db->delete_query("mycode", "1=1");
    echo "<p>Deleted ALL " . $deleted_all . " MyCode entries from database.</p>";
    echo "<p style='color: red;'><strong>WARNING: This removed ALL custom MyCode from your forum!</strong></p>";
}

echo "<hr>";
echo "<div style='background: #f0f8ff; border: 1px solid #0066cc; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
echo "<h4>🔥 Nuclear Option:</h4>";
echo "<p>If the problem persists, you can delete ALL MyCode entries (this will remove all custom BBCode):</p>";
echo "<p><a href='?delete_all=1' onclick='return confirm(\"This will delete ALL MyCode entries! Are you sure?\")' style='background: red; color: white; padding: 10px; text-decoration: none; border-radius: 3px;'>DELETE ALL MYCODE ENTRIES</a></p>";
echo "</div>";

echo "<div style='background: #fffbf0; border: 1px solid #f0ad4e; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
echo "<h4>⚠️ Important:</h4>";
echo "<p><strong>After running this cleanup, you MUST deactivate and reactivate the plugin!</strong></p>";
echo "<p>This ensures the plugin uses the new parsing method without MyCode conflicts.</p>";
echo "</div>";

echo "<p><strong>You can delete this cleanup script file after completing the steps above.</strong></p>";
?> 