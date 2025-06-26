<?php
/**
 * MGW Hide Content - Reset Script
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * This script completely resets the plugin to a clean state
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";
require_once "./inc/functions.php";

if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied.");
}

echo "<h1>🔄 MGW Hide Content - Reset Script</h1>";

if(isset($_GET['reset']) && $_GET['reset'] == 'confirmed')
{
    echo "<h2>🧹 Cleaning up...</h2>";
    
    // 1. Remove ALL MyCode entries (be aggressive)
    $deleted_mycode = $db->delete_query("mycode", "1=1");
    echo "<p>✅ Deleted $deleted_mycode MyCode entries</p>";
    
    // 2. Check if plugin table exists and show tags
    if($db->table_exists("mgw_hide_tags"))
    {
        $query = $db->simple_select("mgw_hide_tags", "*", "", array("order_by" => "tag_name"));
        echo "<h3>Existing Hide Tags:</h3>";
        while($tag = $db->fetch_array($query))
        {
            echo "<p>- [" . htmlspecialchars($tag['tag_name']) . "] - " . htmlspecialchars($tag['tag_description']) . "</p>";
        }
    }
    
    // 3. Recreate MyCode entries for hide tags
    if(function_exists('mgw_hide_update_mycodes'))
    {
        mgw_hide_update_mycodes();
        echo "<p>✅ Recreated MyCode entries for hide tags</p>";
    }
    else
    {
        echo "<p>❌ Plugin function not available - plugin may not be activated</p>";
    }
    
    // 4. Update cache
    if($cache)
    {
        $cache->update_mycode();
        $cache->update_usergroups();
        echo "<p>✅ Updated MyBB cache</p>";
    }
    
    echo "<h2>✅ Reset Complete!</h2>";
    echo "<p><a href='index.php?module=config-mycode'>🔗 Check MyCode Entries</a></p>";
    echo "<p><a href='mgw_hide_panel.php'>🔗 MGW Hide Content Panel</a></p>";
    echo "<p><strong>Now test with a post containing [hide]test content[/hide]</strong></p>";
}
else
{
    echo "<div style='background: #ffebee; border: 2px solid red; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h2>⚠️ WARNING</h2>";
    echo "<p>This will DELETE ALL MyCode entries and recreate only MGW Hide tags!</p>";
    echo "<p>You will lose any custom BBCode you have created.</p>";
    echo "<p><strong>Are you sure you want to continue?</strong></p>";
    echo "<p><a href='?reset=confirmed' onclick='return confirm(\"This will delete ALL MyCode! Continue?\")' style='background: red; color: white; padding: 15px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🔄 YES, RESET EVERYTHING</a></p>";
    echo "</div>";
    
    echo "<h3>Current Status:</h3>";
    echo "<p><strong>Plugin Active:</strong> " . (function_exists('mgw_hide_info') ? '✅ Yes' : '❌ No') . "</p>";
    
    $mycode_count = $db->fetch_field($db->simple_select("mycode", "COUNT(*) as count"), "count");
    echo "<p><strong>MyCode Entries:</strong> $mycode_count</p>";
    
    if($db->table_exists("mgw_hide_tags"))
    {
        $tag_count = $db->fetch_field($db->simple_select("mgw_hide_tags", "COUNT(*) as count"), "count");
        echo "<p><strong>Hide Tags:</strong> $tag_count</p>";
    }
    else
    {
        echo "<p><strong>Hide Tags:</strong> ❌ Table not found</p>";
    }
}
?> 