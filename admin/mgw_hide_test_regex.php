<?php
/**
 * MGW Hide Content - Regex Test Script
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * Test the corrected regex patterns
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";
require_once "./inc/functions.php";

if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied.");
}

echo "<h1>🧪 MGW Hide Content - Regex Pattern Test</h1>";

// Test content
$test_content = "
Public content visible to everyone.

[hide]This content should be hidden from guests and regular users.[/hide]

Some more public content.

[vip]This VIP content should only be visible to VIP members.[/vip]

[mod]Moderator-only information here.[/mod]

End of test content.
";

echo "<h2>📝 Test Content:</h2>";
echo "<pre style='background: #f8f9fa; padding: 15px; border: 1px solid #ddd;'>" . htmlspecialchars($test_content) . "</pre>";

echo "<h2>🔍 Testing Regex Patterns:</h2>";

// Test patterns
$patterns = array(
    'Old problematic pattern' => '#\[hide\](.*?)\[\/hide\]#is',
    'New corrected pattern' => '/\[hide\](.*?)\[\/hide\]/is',
    'MyCode database pattern' => '\[hide\](.*?)\[\/hide\]'
);

foreach($patterns as $name => $pattern)
{
    echo "<h3>Pattern: $name</h3>";
    echo "<p><strong>Pattern:</strong> <code>" . htmlspecialchars($pattern) . "</code></p>";
    
    // For database pattern, we need to add delimiters for testing
    $test_pattern = $pattern;
    if($name == 'MyCode database pattern')
    {
        $test_pattern = '/' . $pattern . '/is';
    }
    
    if(preg_match_all($test_pattern, $test_content, $matches, PREG_SET_ORDER))
    {
        echo "<p><strong>✅ Matches found:</strong> " . count($matches) . "</p>";
        foreach($matches as $i => $match)
        {
            echo "<div style='background: #e8f5e8; padding: 10px; margin: 5px 0; border-left: 4px solid #28a745;'>";
            echo "<strong>Match " . ($i + 1) . ":</strong><br>";
            echo "<strong>Full match:</strong> " . htmlspecialchars($match[0]) . "<br>";
            echo "<strong>Content:</strong> " . htmlspecialchars($match[1]);
            echo "</div>";
        }
    }
    else
    {
        echo "<p><strong>❌ No matches found</strong></p>";
    }
    
    echo "<hr>";
}

echo "<h2>🎯 Expected Results:</h2>";
echo "<ul>";
echo "<li>✅ Should find 3 matches: [hide], [vip], [mod]</li>";
echo "<li>✅ Content inside tags should be extracted correctly</li>";
echo "<li>✅ No weird characters or parsing errors</li>";
echo "</ul>";

echo "<h2>📋 Next Steps if Tests Pass:</h2>";
echo "<ol>";
echo "<li>Deactivate plugin: Admin CP → Plugins → MGW Hide Content → Deactivate</li>";
echo "<li>Reactivate plugin: Admin CP → Plugins → MGW Hide Content → Activate</li>";
echo "<li>Test creating a new hide tag in <a href='mgw_hide_panel.php'>MGW Hide Panel</a></li>";
echo "<li>Create a test post with [hide]test content[/hide]</li>";
echo "<li>Check if content is properly hidden/shown based on user permissions</li>";
echo "</ol>";

echo "<p><strong>🗑️ You can delete this test file after verifying the regex patterns work correctly.</strong></p>";
?> 