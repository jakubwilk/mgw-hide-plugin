<?php
/**
 * MGW Hide Content - New Hook Test Script
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * Test the new parse_message_start hook functionality
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";
require_once "./inc/functions.php";

if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied.");
}

echo "<h1>🧪 MGW Hide Content - New Hook Test (v1.1.0)</h1>";

// Test content
$test_message = "
Public content visible to everyone.

[hide]This content should be hidden from guests and regular users.[/hide]

Some more public content.

[vip]This VIP content should only be visible to VIP members.[/vip]

[mod]Moderator-only information here.[/mod]

End of test content.
";

echo "<h2>📝 Original Message:</h2>";
echo "<pre style='background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo htmlspecialchars($test_message);
echo "</pre>";

// Test our parsing function
if(function_exists('mgw_hide_parse_message_start'))
{
    echo "<h2>🔄 After MGW Hide Processing:</h2>";
    
    // Set up global variables for testing
    global $post;
    $post = array(
        'uid' => $mybb->user['uid'],
        'username' => $mybb->user['username']
    );
    
    $processed_message = mgw_hide_parse_message_start($test_message);
    
    echo "<pre style='background: #e8f5e8; padding: 15px; border: 1px solid #28a745; border-radius: 5px;'>";
    echo htmlspecialchars($processed_message);
    echo "</pre>";
    
    echo "<h2>📋 Current User Info:</h2>";
    echo "<ul>";
    echo "<li><strong>User ID:</strong> " . intval($mybb->user['uid']) . "</li>";
    echo "<li><strong>Username:</strong> " . htmlspecialchars($mybb->user['username']) . "</li>";
    echo "<li><strong>Primary Group:</strong> " . intval($mybb->user['usergroup']) . "</li>";
    echo "<li><strong>Additional Groups:</strong> " . htmlspecialchars($mybb->user['additionalgroups']) . "</li>";
    echo "</ul>";
    
    // Get hide tags
    if(function_exists('mgw_hide_get_tags'))
    {
        $tags = mgw_hide_get_tags();
        echo "<h2>🏷️ Available Hide Tags:</h2>";
        if(empty($tags))
        {
            echo "<p>No hide tags found.</p>";
        }
        else
        {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Tag Name</th><th>Description</th><th>Allowed Groups</th><th>Status</th></tr>";
            foreach($tags as $tag)
            {
                echo "<tr>";
                echo "<td>[" . htmlspecialchars($tag['tag_name']) . "]</td>";
                echo "<td>" . htmlspecialchars($tag['tag_description']) . "</td>";
                echo "<td>" . htmlspecialchars($tag['allowed_groups']) . "</td>";
                echo "<td>" . ($tag['is_active'] ? 'Active' : 'Inactive') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
}
else
{
    echo "<h2>❌ Error:</h2>";
    echo "<p>Function 'mgw_hide_parse_message_start' not found. Make sure the plugin is activated.</p>";
}

echo "<hr>";
echo "<p><a href='mgw_hide_panel.php'>← Back to MGW Hide Panel</a></p>";
?> 