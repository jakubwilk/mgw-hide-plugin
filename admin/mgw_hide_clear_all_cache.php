<?php
/**
 * MGW Hide Content - Clear ALL Cache
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * Clear all possible caches to resolve persistent issues
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";

if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied.");
}

echo "<h1>🧹 Clear ALL MyBB Cache</h1>";

echo "<h2>💾 Clearing all caches...</h2>";

$cleared = array();

// 1. Clear MyCode cache
if($cache)
{
    $cache->update_mycode();
    $cleared[] = "MyCode cache";
    
    // 2. Clear user groups cache
    $cache->update_usergroups();
    $cleared[] = "User groups cache";
    
    // 3. Clear forums cache  
    $cache->update_forumlist();
    $cleared[] = "Forum list cache";
    
    // 4. Clear forum cache
    $cache->update_forums();
    $cleared[] = "Forums cache";
    
    // 5. Clear moderators cache
    $cache->update_moderators();
    $cleared[] = "Moderators cache";
    
    // 6. Clear admin sessions cache
    $cache->update_adminsessions();
    $cleared[] = "Admin sessions cache";
    
    // 7. Clear settings cache
    $cache->update_settings();
    $cleared[] = "Settings cache";
}

// 8. Manual cache clearing
$cache_files = array(
    'settings',
    'usergroups', 
    'forumlist',
    'forums',
    'moderators',
    'mycode',
    'adminsessions'
);

foreach($cache_files as $cache_name)
{
    $cache_file = MYBB_ROOT . "cache/{$cache_name}.php";
    if(file_exists($cache_file))
    {
        @unlink($cache_file);
        $cleared[] = "Manual: {$cache_name}.php";
    }
}

// 9. Clear datacache table
$db->delete_query("datacache", "1=1");
$cleared[] = "Database datacache table";

echo "<h3>✅ Cleared caches:</h3>";
foreach($cleared as $item)
{
    echo "<p>✅ {$item}</p>";
}

echo "<div style='background: #d4edda; border: 2px solid #155724; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>🎉 ALL CACHES CLEARED!</h3>";
echo "<p><strong>Now do the following IMMEDIATELY:</strong></p>";
echo "<ol>";
echo "<li><strong>Clear browser cache:</strong> Press Ctrl+F5 (or Cmd+Shift+R on Mac)</li>";
echo "<li><strong>Try incognito/private browsing mode</strong></li>";
echo "<li><strong>Test the [hide] tag again</strong></li>";
echo "<li>If still doesn't work, <strong>deactivate and reactivate the plugin</strong></li>";
echo "</ol>";
echo "</div>";

// 10. Force plugin reactivation
echo "<h2>🔄 Force Plugin Refresh</h2>";
echo "<p>If cache clearing doesn't help, we need to force refresh the plugin:</p>";

if(isset($_GET['force_refresh']) && $_GET['force_refresh'] == 'yes')
{
    echo "<p>🔄 Forcing plugin refresh...</p>";
    
    // Clear any remaining problematic data
    $db->delete_query("mycode", "replacement LIKE '%HANDLED BY MGW HIDE PLUGIN%'");
    echo "<p>✅ Removed any remaining problematic MyCode</p>";
    
    // Clear hooks cache if exists
    if(file_exists(MYBB_ROOT . "cache/plugins.php"))
    {
        @unlink(MYBB_ROOT . "cache/plugins.php");
        echo "<p>✅ Cleared plugins cache</p>";
    }
    
    echo "<p style='color: green; font-weight: bold;'>✅ Plugin refresh complete!</p>";
    echo "<p><strong>Now test the [hide] tag!</strong></p>";
}
else
{
    echo "<p><a href='?force_refresh=yes' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px;'>🔄 Force Plugin Refresh</a></p>";
}

echo "<hr>";
echo "<h3>🔍 Debug Info After Cache Clear:</h3>";
echo "<p><strong>Current time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>MyCode entries:</strong> " . $db->fetch_field($db->simple_select("mycode", "COUNT(*) as count"), "count") . "</p>";
echo "<p><strong>Datacache entries:</strong> " . $db->fetch_field($db->simple_select("datacache", "COUNT(*) as count"), "count") . "</p>";

echo "<p><a href='mgw_hide_test_parsing.php'>🧪 Test Parsing Again</a></p>";
echo "<p><a href='mgw_hide_panel.php'>← Back to Panel</a></p>";
?> 