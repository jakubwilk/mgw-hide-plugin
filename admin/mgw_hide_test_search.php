<?php
/**
 * MGW Hide Content - Search Results Test
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * Test script to debug search results hook issue
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";
require_once "./inc/functions.php";

if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied.");
}

echo "<h1>🔍 MGW Hide Search Results - Debug Test</h1>";

// Include plugin functions
if(file_exists("../inc/plugins/mgw_hide.php"))
{
    include_once "../inc/plugins/mgw_hide.php";
}

echo "<h2>Test 1: Function exists</h2>";
if(function_exists('mgw_hide_search_results'))
{
    echo "<p>✅ Function mgw_hide_search_results exists</p>";
}
else
{
    echo "<p>❌ Function mgw_hide_search_results NOT found</p>";
    exit;
}

echo "<h2>Test 2: Test with array parameter</h2>";
$test_post_array = array(
    'pid' => 123,
    'message' => 'This is public content. [hide]This is hidden content[/hide] More public content.',
    'uid' => 1,
    'username' => 'testuser'
);

echo "<p><strong>Input:</strong></p>";
echo "<pre>" . htmlspecialchars(print_r($test_post_array, true)) . "</pre>";

try {
    $result = mgw_hide_search_results($test_post_array);
    echo "<p><strong>✅ Success - Result:</strong></p>";
    echo "<pre>" . htmlspecialchars(print_r($result, true)) . "</pre>";
} catch(Exception $e) {
    echo "<p><strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Test 3: Test with string parameter</h2>";
$test_string = 'This is just a string';

echo "<p><strong>Input:</strong> " . htmlspecialchars($test_string) . "</p>";

try {
    $result = mgw_hide_search_results($test_string);
    echo "<p><strong>✅ Success - Result:</strong> " . htmlspecialchars($result) . "</p>";
} catch(Exception $e) {
    echo "<p><strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Test 4: Test with empty parameter</h2>";
try {
    $result = mgw_hide_search_results('');
    echo "<p><strong>✅ Success - Result:</strong> '" . htmlspecialchars($result) . "'</p>";
} catch(Exception $e) {
    echo "<p><strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Test 5: Test with null parameter</h2>";
try {
    $result = mgw_hide_search_results(null);
    echo "<p><strong>✅ Success - Result:</strong> " . var_export($result, true) . "</p>";
} catch(Exception $e) {
    echo "<p><strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Current User Info</h2>";
echo "<ul>";
echo "<li><strong>User ID:</strong> " . intval($mybb->user['uid']) . "</li>";
echo "<li><strong>Username:</strong> " . htmlspecialchars($mybb->user['username']) . "</li>";
echo "<li><strong>User Group:</strong> " . intval($mybb->user['usergroup']) . "</li>";
echo "</ul>";

echo "<h2>Plugin Settings</h2>";
echo "<ul>";
echo "<li><strong>Enabled:</strong> " . (isset($mybb->settings['mgw_hide_enabled']) ? ($mybb->settings['mgw_hide_enabled'] ? 'Yes' : 'No') : 'Not set') . "</li>";
echo "</ul>";

echo "<h2>Available Hide Tags</h2>";
if(function_exists('mgw_hide_get_tags'))
{
    $tags = mgw_hide_get_tags();
    if(empty($tags))
    {
        echo "<p>No active hide tags found.</p>";
    }
    else
    {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Tag Name</th><th>Allowed Groups</th><th>Active</th></tr>";
        foreach($tags as $tag)
        {
            echo "<tr>";
            echo "<td>[" . htmlspecialchars($tag['tag_name']) . "]</td>";
            echo "<td>" . htmlspecialchars($tag['allowed_groups']) . "</td>";
            echo "<td>" . ($tag['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}
else
{
    echo "<p>Function mgw_hide_get_tags not found.</p>";
}

echo "<p><a href='mgw_hide_panel.php'>← Back to MGW Hide Panel</a></p>";
?> 