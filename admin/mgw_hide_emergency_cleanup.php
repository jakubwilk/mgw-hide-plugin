<?php
/**
 * MGW Hide Content - Emergency Cleanup
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * EMERGENCY SCRIPT: Removes ALL problematic MyCode entries that cause [HANDLED BY MGW HIDE PLUGIN] text
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";

if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied.");
}

echo "<h1>🚨 MGW Hide - Emergency Cleanup v2</h1>";

// Show ALL MyCode entries first
echo "<h2>📋 Current MyCode Entries:</h2>";
$query = $db->simple_select("mycode", "*", "", array("order_by" => "cid"));
$total_entries = 0;
$problematic_entries = array();

while($entry = $db->fetch_array($query))
{
    $total_entries++;
    $is_problematic = false;
    
    // More aggressive detection
    if(stripos($entry['title'], 'hide') !== false || 
       stripos($entry['title'], 'mgw') !== false ||
       stripos($entry['replacement'], 'HANDLED BY MGW HIDE PLUGIN') !== false ||
       stripos($entry['replacement'], 'PLUGIN') !== false ||
       stripos($entry['regex'], 'hide') !== false)
    {
        $is_problematic = true;
        $problematic_entries[] = $entry['cid'];
    }
    
    $color = $is_problematic ? 'style="background: #ffebee; border: 2px solid red; color: red;"' : 'style="background: #e8f5e8; border: 1px solid #ccc;"';
    
    echo "<div $color style='margin: 5px 0; padding: 8px; border-radius: 3px; font-size: 12px;'>";
    echo "<strong>ID:</strong> " . $entry['cid'] . " | ";
    echo "<strong>Title:</strong> " . htmlspecialchars($entry['title']) . " | ";
    echo "<strong>Active:</strong> " . ($entry['active'] ? '✅' : '❌') . "<br>";
    echo "<strong>Regex:</strong> " . htmlspecialchars(substr($entry['regex'], 0, 100)) . "<br>";
    echo "<strong>Replacement:</strong> " . htmlspecialchars(substr($entry['replacement'], 0, 100));
    if(strlen($entry['replacement']) > 100) echo "...";
    echo "</div>";
}

echo "<p><strong>Total MyCode entries: {$total_entries}</strong></p>";
echo "<p><strong>Problematic entries: " . count($problematic_entries) . "</strong></p>";

if(count($problematic_entries) > 0)
{
    echo "<h2>🗑️ Deleting ALL problematic entries...</h2>";
    
    // Delete by specific IDs first
    foreach($problematic_entries as $cid)
    {
        $deleted = $db->delete_query("mycode", "cid = '" . intval($cid) . "'");
        echo "<p>✅ Deleted MyCode ID: {$cid} → Result: {$deleted}</p>";
    }
    
    // Then delete by patterns (more aggressive)
    $conditions = array(
        "replacement LIKE '%HANDLED BY MGW HIDE PLUGIN%'",
        "replacement LIKE '%PLUGIN%'",
        "title LIKE '%MGW Hide%'",
        "title LIKE '%hide%'",
        "regex LIKE '%hide%'",
        "regex LIKE '%MGW%'"
    );
    
    $total_pattern_deleted = 0;
    foreach($conditions as $condition)
    {
        $deleted = $db->delete_query("mycode", $condition);
        echo "<p>✅ Pattern: {$condition} → Deleted: {$deleted}</p>";
        $total_pattern_deleted += $deleted;
    }
    
    echo "<h3>✅ Total deleted: " . (count($problematic_entries) + $total_pattern_deleted) . " entries</h3>";
    
    // Clear ALL caches
    if(isset($cache))
    {
        $cache->update_mycode();
        $cache->update_usergroups();
        $cache->update_forumlist();
        $cache->update_forums();
        echo "<p>✅ ALL MyBB caches updated</p>";
    }
    
    // Verify cleanup
    $remaining_query = $db->simple_select("mycode", "COUNT(*) as count", "replacement LIKE '%HANDLED BY MGW HIDE PLUGIN%' OR title LIKE '%MGW Hide%' OR title LIKE '%hide%'");
    $remaining_count = $db->fetch_field($remaining_query, "count");
    
    if($remaining_count > 0)
    {
        echo "<p style='color: red;'>⚠️ WARNING: {$remaining_count} problematic entries still remain!</p>";
        
        // Show remaining entries
        $remaining_entries = $db->simple_select("mycode", "*", "replacement LIKE '%HANDLED BY MGW HIDE PLUGIN%' OR title LIKE '%MGW Hide%' OR title LIKE '%hide%'");
        echo "<h4>Remaining problematic entries:</h4>";
        while($entry = $db->fetch_array($remaining_entries))
        {
            echo "<p style='background: #ffebee; padding: 5px; border: 1px solid red;'>";
            echo "ID: {$entry['cid']} - Title: " . htmlspecialchars($entry['title']) . " - Replacement: " . htmlspecialchars(substr($entry['replacement'], 0, 50));
            echo "</p>";
        }
        
        echo "<p><a href='?nuclear=1' style='background: darkred; color: white; padding: 10px; text-decoration: none; border-radius: 3px; font-weight: bold;'>🔥 NUCLEAR OPTION: DELETE ALL MYCODE</a></p>";
    }
    else
    {
        echo "<p style='color: green; font-weight: bold;'>✅ All problematic entries successfully removed!</p>";
    }
    
    echo "<div style='background: #d4edda; border: 2px solid #155724; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3>🎉 CLEANUP COMPLETE!</h3>";
    echo "<p><strong>Now IMMEDIATELY do the following:</strong></p>";
    echo "<ol>";
    echo "<li>Go to <strong>Configuration → Plugins</strong></li>";
    echo "<li><strong>Deactivate</strong> MGW Hide Content plugin</li>";
    echo "<li><strong>Activate</strong> MGW Hide Content plugin again</li>";
    echo "<li>Clear browser cache (Ctrl+F5)</li>";
    echo "<li>Test with a simple post: <code>[hide]test content[/hide]</code></li>";
    echo "</ol>";
    echo "</div>";
}
else
{
    echo "<p>✅ No problematic entries found!</p>";
    echo "<p>The issue might be in browser cache or post cache. Try:</p>";
    echo "<ol>";
    echo "<li>Clear browser cache (Ctrl+F5)</li>";
    echo "<li>Deactivate/reactivate plugin</li>";
    echo "<li>Try in incognito/private browsing mode</li>";
    echo "</ol>";
}

// Nuclear option
if(isset($_GET['nuclear']) && $_GET['nuclear'] == '1')
{
    echo "<h2 style='color: darkred;'>🔥 NUCLEAR OPTION - Deleting ALL MyCode entries</h2>";
    $deleted_all = $db->delete_query("mycode", "1=1");
    echo "<p style='color: red; font-weight: bold;'>Deleted ALL {$deleted_all} MyCode entries from database!</p>";
    echo "<p style='color: red;'><strong>WARNING: This removed ALL custom MyCode from your forum!</strong></p>";
    
    if(isset($cache))
    {
        $cache->update_mycode();
        echo "<p>✅ Cache updated</p>";
    }
}

echo "<hr>";
echo "<p><a href='mgw_hide_panel.php'>← Back to MGW Hide Panel</a></p>";
echo "<p><a href='mgw_hide_debug.php'>🔍 Run Debug Script</a></p>";
echo "<p><a href='index.php?module=config-mycode'>📋 Check MyCode in ACP</a></p>";
?> 