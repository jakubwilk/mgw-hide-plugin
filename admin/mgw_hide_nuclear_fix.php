<?php
/**
 * MGW Hide Content - Nuclear Fix
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * NUCLEAR OPTION: Deletes ALL MyCode entries to fix the [HANDLED BY MGW HIDE PLUGIN] issue
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";

if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied.");
}

echo "<h1>🔥 Nuclear Fix - Delete ALL MyCode</h1>";

// Count all MyCode entries
$total_count = $db->fetch_field($db->simple_select("mycode", "COUNT(*) as count"), "count");
echo "<p><strong>Current MyCode entries in database: {$total_count}</strong></p>";

if(isset($_GET['confirm']) && $_GET['confirm'] == 'yes')
{
    echo "<h2>💥 DELETING ALL MYCODE ENTRIES...</h2>";
    
    // Show what we're deleting
    $query = $db->simple_select("mycode", "*", "", array("order_by" => "cid"));
    while($entry = $db->fetch_array($query))
    {
        echo "<p style='font-size: 12px; background: #ffebee; padding: 5px; border: 1px solid red;'>";
        echo "Deleting ID: {$entry['cid']} - Title: " . htmlspecialchars($entry['title']) . " - Regex: " . htmlspecialchars(substr($entry['regex'], 0, 50));
        echo "</p>";
    }
    
    // DELETE ALL
    $deleted = $db->delete_query("mycode", "1=1");
    
    echo "<h2 style='color: green;'>✅ SUCCESS!</h2>";
    echo "<p><strong>Deleted {$deleted} MyCode entries.</strong></p>";
    
    // Clear cache
    if(isset($cache))
    {
        $cache->update_mycode();
        $cache->update_usergroups();
        echo "<p>✅ Cache updated</p>";
    }
    
    echo "<div style='background: #d4edda; border: 2px solid #155724; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3>🎉 PROBLEM SOLVED!</h3>";
    echo "<p><strong>All MyCode entries have been removed!</strong></p>";
    echo "<p><strong>Now test your [hide] tags - they should work perfectly!</strong></p>";
    echo "<ol>";
    echo "<li>Go to your forum</li>";
    echo "<li>Create a new post with: <code>[hide]Test message[/hide]</code></li>";
    echo "<li>It should work correctly now!</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<p><strong>Note:</strong> You have lost any custom BBCode, but MGW Hide Content will work properly now.</p>";
}
else
{
    echo "<div style='background: #fff3cd; border: 2px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h2>⚠️ WARNING</h2>";
    echo "<p><strong>This will DELETE ALL MyCode entries from your database!</strong></p>";
    echo "<p>This includes:</p>";
    echo "<ul>";
    echo "<li>All custom BBCode tags you may have created</li>";
    echo "<li>Any problematic entries causing the [HANDLED BY MGW HIDE PLUGIN] issue</li>";
    echo "<li>Standard MyBB BBCode entries</li>";
    echo "</ul>";
    echo "<p><strong>Are you sure you want to continue?</strong></p>";
    echo "<p style='margin-top: 20px;'>";
    echo "<a href='?confirm=yes' onclick='return confirm(\"This will delete ALL MyCode entries! Are you absolutely sure?\")' style='background: darkred; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;'>🔥 YES, DELETE ALL MYCODE</a>";
    echo "</p>";
    echo "</div>";
    
    // Show current MyCode entries
    if($total_count > 0)
    {
        echo "<h3>Current MyCode entries that will be deleted:</h3>";
        $query = $db->simple_select("mycode", "*", "", array("order_by" => "cid"));
        while($entry = $db->fetch_array($query))
        {
            $is_problematic = (stripos($entry['title'], 'hide') !== false || 
                              stripos($entry['replacement'], 'HANDLED BY MGW HIDE PLUGIN') !== false);
            $color = $is_problematic ? 'background: #ffebee; border: 2px solid red;' : 'background: #f8f9fa; border: 1px solid #ccc;';
            
            echo "<div style='{$color} margin: 5px 0; padding: 8px; border-radius: 3px; font-size: 12px;'>";
            echo "<strong>ID:</strong> {$entry['cid']} | ";
            echo "<strong>Title:</strong> " . htmlspecialchars($entry['title']) . " | ";
            echo "<strong>Active:</strong> " . ($entry['active'] ? 'Yes' : 'No') . "<br>";
            echo "<strong>Regex:</strong> " . htmlspecialchars(substr($entry['regex'], 0, 100)) . "<br>";
            echo "<strong>Replacement:</strong> " . htmlspecialchars(substr($entry['replacement'], 0, 100));
            if(strlen($entry['replacement']) > 100) echo "...";
            echo "</div>";
        }
    }
}

echo "<hr>";
echo "<p><a href='mgw_hide_emergency_cleanup.php'>🧹 Try Emergency Cleanup First</a></p>";
echo "<p><a href='mgw_hide_panel.php'>← Back to Panel</a></p>";
?> 