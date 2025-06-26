<?php
/**
 * MGW Hide Content - Live Search Test
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * Test search results processing in real-time
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";
require_once "./inc/functions.php";

if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied.");
}

// Include plugin functions
if(file_exists("../inc/plugins/mgw_hide.php"))
{
    include_once "../inc/plugins/mgw_hide.php";
}

echo "<h1>🔍 MGW Hide - Live Search Test</h1>";

// Get posts with hide tags from database
$query = $db->simple_select("posts", "pid, message, subject, uid", "message LIKE '%[hide]%' OR message LIKE '%[/hide]%'", array("limit" => 5));

echo "<h2>📋 Posts containing [hide] tags:</h2>";

if($db->num_rows($query) == 0)
{
    echo "<p>No posts found with [hide] tags.</p>";
}
else
{
    while($post = $db->fetch_array($query))
    {
        echo "<div style='border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px;'>";
        echo "<h3>Post ID: " . $post['pid'] . " | Subject: " . htmlspecialchars($post['subject']) . "</h3>";
        
        echo "<h4>Original Message:</h4>";
        echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 3px;'>";
        echo htmlspecialchars($post['message']);
        echo "</div>";
        
        // Test our search function
        echo "<h4>After mgw_hide_search_results():</h4>";
        $test_post = array(
            'pid' => $post['pid'],
            'message' => $post['message'],
            'uid' => $post['uid']
        );
        
        try {
            $processed = mgw_hide_search_results($test_post);
            echo "<div style='background: #e8f5e8; padding: 10px; border-radius: 3px;'>";
            echo htmlspecialchars($processed['message']);
            echo "</div>";
        } catch(Exception $e) {
            echo "<div style='background: #ffebee; padding: 10px; border-radius: 3px; color: red;'>";
            echo "Error: " . htmlspecialchars($e->getMessage());
            echo "</div>";
        }
        
        // Test our main parsing function directly
        echo "<h4>After mgw_hide_parse_message_start():</h4>";
        try {
            $parsed = mgw_hide_parse_message_start($post['message']);
            echo "<div style='background: #e3f2fd; padding: 10px; border-radius: 3px;'>";
            echo htmlspecialchars($parsed);
            echo "</div>";
        } catch(Exception $e) {
            echo "<div style='background: #ffebee; padding: 10px; border-radius: 3px; color: red;'>";
            echo "Error: " . htmlspecialchars($e->getMessage());
            echo "</div>";
        }
        
        echo "</div>";
    }
}

echo "<h2>🔧 Current User Info</h2>";
echo "<ul>";
echo "<li><strong>User ID:</strong> " . intval($mybb->user['uid']) . "</li>";
echo "<li><strong>Username:</strong> " . htmlspecialchars($mybb->user['username']) . "</li>";
echo "<li><strong>User Group:</strong> " . intval($mybb->user['usergroup']) . "</li>";
echo "<li><strong>Additional Groups:</strong> " . htmlspecialchars($mybb->user['additionalgroups']) . "</li>";
echo "</ul>";

echo "<h2>⚙️ Plugin Settings</h2>";
echo "<ul>";
echo "<li><strong>Enabled:</strong> " . (isset($mybb->settings['mgw_hide_enabled']) ? ($mybb->settings['mgw_hide_enabled'] ? 'Yes' : 'No') : 'Not set') . "</li>";
echo "<li><strong>Message:</strong> " . (isset($mybb->settings['mgw_hide_show_message']) ? htmlspecialchars($mybb->settings['mgw_hide_show_message']) : 'Not set') . "</li>";
echo "</ul>";

echo "<h2>🏷️ Available Hide Tags</h2>";
if(function_exists('mgw_hide_get_tags'))
{
    $tags = mgw_hide_get_tags();
    if(empty($tags))
    {
        echo "<p>❌ No active hide tags found.</p>";
    }
    else
    {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background: #f8f9fa;'><th>Tag Name</th><th>Allowed Groups</th><th>Custom Message</th><th>Active</th></tr>";
        foreach($tags as $tag)
        {
            echo "<tr>";
            echo "<td>[" . htmlspecialchars($tag['tag_name']) . "]</td>";
            echo "<td>" . htmlspecialchars($tag['allowed_groups']) . "</td>";
            echo "<td>" . (empty($tag['custom_message']) ? '<em>Global</em>' : htmlspecialchars(substr($tag['custom_message'], 0, 50)) . '...') . "</td>";
            echo "<td>" . ($tag['is_active'] ? '✅' : '❌') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

echo "<h2>💡 Troubleshooting</h2>";
echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>If tags are still visible in search results:</strong></p>";
echo "<ol>";
echo "<li>Check if the plugin is activated (not just installed)</li>";
echo "<li>Clear MyBB cache completely</li>";
echo "<li>Check if user has permissions to see the content</li>";
echo "<li>Verify that <code>parse_message_start</code> hook is working</li>";
echo "</ol>";
echo "</div>";

echo "<p><a href='mgw_hide_panel.php'>← Back to MGW Hide Panel</a></p>";
?> 