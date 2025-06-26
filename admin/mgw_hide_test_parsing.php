<?php
/**
 * MGW Hide Content - Parsing Test
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * Test script to debug parsing issues
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";

if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied.");
}

echo "<h1>🧪 MGW Hide Content - Parsing Test</h1>";

// Test content
$test_content = "[hide]Testowa wiadomość[/hide]";

echo "<h2>📝 Original Content:</h2>";
echo "<pre style='background: #f8f9fa; padding: 10px; border: 1px solid #ccc;'>" . htmlspecialchars($test_content) . "</pre>";

// Test our parsing function directly
echo "<h2>🔧 Testing Our Parse Function:</h2>";

if(function_exists('mgw_hide_parse_message_start'))
{
    echo "<p>✅ Function exists</p>";
    
    $parsed_content = mgw_hide_parse_message_start($test_content);
    echo "<p><strong>Result:</strong></p>";
    echo "<pre style='background: #e8f5e8; padding: 10px; border: 1px solid #28a745;'>" . htmlspecialchars($parsed_content) . "</pre>";
}
else
{
    echo "<p>❌ Function NOT found - plugin not activated?</p>";
}

// Test plugin settings
echo "<h2>⚙️ Plugin Settings:</h2>";
echo "<p><strong>Enabled:</strong> " . (isset($mybb->settings['mgw_hide_enabled']) ? ($mybb->settings['mgw_hide_enabled'] ? '✅ Yes' : '❌ No') : '❓ Not set') . "</p>";
echo "<p><strong>Message:</strong> " . (isset($mybb->settings['mgw_hide_show_message']) ? htmlspecialchars($mybb->settings['mgw_hide_show_message']) : '❓ Not set') . "</p>";

// Test hide tags
echo "<h2>🏷️ Hide Tags in Database:</h2>";
if($db->table_exists("mgw_hide_tags"))
{
    $query = $db->simple_select("mgw_hide_tags", "*", "", array("order_by" => "tag_name"));
    $tag_count = 0;
    while($tag = $db->fetch_array($query))
    {
        $tag_count++;
        echo "<div style='background: #f0f8ff; border: 1px solid #0066cc; margin: 5px 0; padding: 10px; border-radius: 3px;'>";
        echo "<strong>Tag:</strong> [" . htmlspecialchars($tag['tag_name']) . "] | ";
        echo "<strong>Active:</strong> " . ($tag['is_active'] ? '✅' : '❌') . " | ";
        echo "<strong>Groups:</strong> " . htmlspecialchars($tag['allowed_groups']) . "<br>";
        echo "<strong>Description:</strong> " . htmlspecialchars($tag['tag_description']);
        echo "</div>";
    }
    echo "<p>Total tags: {$tag_count}</p>";
}
else
{
    echo "<p>❌ Table does NOT exist</p>";
}

// Test current user permissions
echo "<h2>👤 Current User Info:</h2>";
echo "<p><strong>UID:</strong> " . $mybb->user['uid'] . "</p>";
echo "<p><strong>Username:</strong> " . htmlspecialchars($mybb->user['username']) . "</p>";
echo "<p><strong>Primary Group:</strong> " . $mybb->user['usergroup'] . "</p>";
echo "<p><strong>Additional Groups:</strong> " . htmlspecialchars($mybb->user['additionalgroups']) . "</p>";

// Test permission check
if(function_exists('mgw_hide_get_tags') && function_exists('mgw_hide_user_can_see'))
{
    echo "<h2>🔐 Permission Test:</h2>";
    $tags = mgw_hide_get_tags();
    foreach($tags as $tag)
    {
        $can_see = mgw_hide_user_can_see($tag);
        echo "<p><strong>[{$tag['tag_name']}]:</strong> " . ($can_see ? '✅ Can see' : '❌ Cannot see') . "</p>";
    }
}

// Test MyCode processing
echo "<h2>🔤 MyCode Processing Test:</h2>";

// Simulate what MyBB would do
require_once "../inc/class_parser.php";
$parser = new postParser;

echo "<h3>Step 1: Original text</h3>";
echo "<pre>" . htmlspecialchars($test_content) . "</pre>";

echo "<h3>Step 2: After parse_message</h3>";
$parsed_by_mybb = $parser->parse_message($test_content, array(
    'allow_mycode' => 1,
    'allow_smilies' => 1,
    'allow_imgcode' => 1,
    'allow_videocode' => 1,
    'filter_badwords' => 1
));
echo "<pre>" . htmlspecialchars($parsed_by_mybb) . "</pre>";

echo "<h3>Step 3: Our hook should have processed it before MyCode</h3>";
if(function_exists('mgw_hide_parse_message_start'))
{
    $our_result = mgw_hide_parse_message_start($test_content);
    echo "<pre>" . htmlspecialchars($our_result) . "</pre>";
    
    echo "<h3>Step 4: If we process first, then MyBB processes our result</h3>";
    $final_result = $parser->parse_message($our_result, array(
        'allow_mycode' => 1,
        'allow_smilies' => 1,
        'allow_imgcode' => 1,
        'allow_videocode' => 1,
        'filter_badwords' => 1
    ));
    echo "<pre>" . htmlspecialchars($final_result) . "</pre>";
}

echo "<hr>";
echo "<p><a href='mgw_hide_emergency_cleanup.php'>🧹 Run Emergency Cleanup</a></p>";
echo "<p><a href='mgw_hide_debug.php'>🔍 Full Debug Script</a></p>";
echo "<p><a href='mgw_hide_panel.php'>← Back to Panel</a></p>";
?> 