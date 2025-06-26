<?php
/**
 * MGW Hide Content - Force Fix for Search Results
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * This script will patch search.php or apply alternative fixes
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";
require_once "./inc/functions.php";

if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied.");
}

echo "<h1>🔧 MGW Hide - Force Fix for Search Results</h1>";

// Include plugin functions
if(file_exists("../inc/plugins/mgw_hide.php"))
{
    include_once "../inc/plugins/mgw_hide.php";
}

echo "<div style='background: #ffebee; border: 1px solid #f44336; color: #c62828; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
echo "<h3>⚠️ Warning</h3>";
echo "<p>This script applies alternative fixes when normal hooks don't work. Use only if standard methods failed.</p>";
echo "</div>";

$action = isset($_GET['fix']) ? $_GET['fix'] : '';

if($action == '')
{
    echo "<h2>🎯 Available Fixes</h2>";
    
    echo "<div style='border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px;'>";
    echo "<h3>Fix 1: Add search output buffer</h3>";
    echo "<p>Intercepts search output and processes it before display.</p>";
    echo "<p><a href='?fix=output_buffer' style='background: #2196F3; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px;'>Apply Fix 1</a></p>";
    echo "</div>";
    
    echo "<div style='border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px;'>";
    echo "<h3>Fix 2: Template modification</h3>";
    echo "<p>Modifies search result templates to hide [hide] tags.</p>";
    echo "<p><a href='?fix=template_mod' style='background: #FF9800; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px;'>Apply Fix 2</a></p>";
    echo "</div>";
    
    echo "<div style='border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px;'>";
    echo "<h3>Fix 3: JavaScript post-processing</h3>";
    echo "<p>Uses JavaScript to hide [hide] tags after page loads.</p>";
    echo "<p><a href='?fix=javascript' style='background: #4CAF50; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px;'>Apply Fix 3</a></p>";
    echo "</div>";
}
elseif($action == 'output_buffer')
{
    echo "<h2>🔧 Fix 1: Output Buffer Method</h2>";
    
    $buffer_code = '<?php
// MGW Hide Content - Search Output Buffer Fix
// Add this to the end of search.php (before final ?>)

if(isset($_GET[\'action\']) && $_GET[\'action\'] == \'results\')
{
    // Start output buffering
    ob_start(function($buffer) {
        global $mybb, $db;
        
        // Only process if MGW Hide is enabled
        if(isset($mybb->settings[\'mgw_hide_enabled\']) && $mybb->settings[\'mgw_hide_enabled\'])
        {
            // Include plugin functions
            if(file_exists("inc/plugins/mgw_hide.php"))
            {
                include_once "inc/plugins/mgw_hide.php";
            }
            
            // Get hide tags
            if(function_exists(\'mgw_hide_get_tags\'))
            {
                $tags = mgw_hide_get_tags();
                
                foreach($tags as $tag)
                {
                    if(!$tag[\'is_active\']) continue;
                    
                    $pattern = \'/\\[\' . preg_quote($tag[\'tag_name\'], \'/\') . \'\\](.*?)\\[\\/\' . preg_quote($tag[\'tag_name\'], \'/\') . \'\\]/is\';
                    
                    if(function_exists(\'mgw_hide_user_can_see\') && !mgw_hide_user_can_see($tag))
                    {
                        $custom_message = trim($tag[\'custom_message\']);
                        if(!empty($custom_message))
                        {
                            $replacement = $custom_message;
                        }
                        else
                        {
                            $replacement = \'<span style="color: #999; font-style: italic;">[Hidden Content]</span>\';
                        }
                        $buffer = preg_replace($pattern, $replacement, $buffer);
                    }
                    else
                    {
                        // Remove tags but show content
                        $buffer = preg_replace($pattern, \'$1\', $buffer);
                    }
                }
            }
        }
        
        return $buffer;
    });
}
?>';
    
    echo "<p>Add this code to the end of your <code>search.php</code> file:</p>";
    echo "<textarea style='width: 100%; height: 300px; font-family: monospace;'>";
    echo htmlspecialchars($buffer_code);
    echo "</textarea>";
    
    echo "<div style='background: #e8f5e8; border: 1px solid #4caf50; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
    echo "<h4>✅ Instructions:</h4>";
    echo "<ol>";
    echo "<li>Copy the code above</li>";
    echo "<li>Open <code>search.php</code> file</li>";
    echo "<li>Paste the code at the very end (before closing <code>?&gt;</code>)</li>";
    echo "<li>Save the file</li>";
    echo "<li>Test search results</li>";
    echo "</ol>";
    echo "</div>";
}
elseif($action == 'template_mod')
{
    echo "<h2>🔧 Fix 2: Template Modification</h2>";
    
    // Check if search templates exist
    $templates_to_check = array('search_results_posts_post', 'search_results_posts', 'search_results');
    
    echo "<p>Checking search templates...</p>";
    
    foreach($templates_to_check as $template_name)
    {
        $query = $db->simple_select("templates", "template, title", "title = '" . $db->escape_string($template_name) . "'");
        if($db->num_rows($query) > 0)
        {
            $template = $db->fetch_array($query);
            echo "<div style='border: 1px solid #ddd; margin: 10px 0; padding: 10px; border-radius: 5px;'>";
            echo "<h4>Template: " . htmlspecialchars($template['title']) . "</h4>";
            echo "<p>Template found and can be modified to hide [hide] tags.</p>";
            
            if(isset($_GET['modify']) && $_GET['modify'] == $template_name)
            {
                // Apply template modification
                $modified_template = $template['template'];
                
                // Add JavaScript to hide [hide] tags
                $js_code = '<script>
document.addEventListener("DOMContentLoaded", function() {
    var content = document.body.innerHTML;
    content = content.replace(/\\[hide\\](.*?)\\[\\/hide\\]/gis, "<span style=\\"color: #999; font-style: italic;\\">[Hidden Content]</span>");
    document.body.innerHTML = content;
});
</script>';
                
                $modified_template = $modified_template . $js_code;
                
                $db->update_query("templates", array('template' => $db->escape_string($modified_template)), "title = '" . $db->escape_string($template_name) . "'");
                
                echo "<p style='color: green;'>✅ Template modified successfully!</p>";
            }
            else
            {
                echo "<p><a href='?fix=template_mod&modify=" . urlencode($template_name) . "' style='background: #ff9800; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;'>Modify This Template</a></p>";
            }
            echo "</div>";
        }
        else
        {
            echo "<p>❌ Template <code>" . htmlspecialchars($template_name) . "</code> not found.</p>";
        }
    }
}
elseif($action == 'javascript')
{
    echo "<h2>🔧 Fix 3: JavaScript Method</h2>";
    
    $js_code = '<script>
// MGW Hide Content - JavaScript Fix for Search Results
document.addEventListener("DOMContentLoaded", function() {
    // Only run on search results page
    if(window.location.href.indexOf("search.php") !== -1 && window.location.href.indexOf("action=results") !== -1) {
        
        // Find all text nodes containing [hide] tags
        function processTextNodes() {
            var walker = document.createTreeWalker(
                document.body,
                NodeFilter.SHOW_TEXT,
                null,
                false
            );
            
            var textNodes = [];
            var node;
            
            while(node = walker.nextNode()) {
                if(node.textContent.indexOf("[hide]") !== -1 || node.textContent.indexOf("[/hide]") !== -1) {
                    textNodes.push(node);
                }
            }
            
            // Process each text node
            textNodes.forEach(function(textNode) {
                var content = textNode.textContent;
                
                // Replace [hide]...[/hide] with hidden message
                content = content.replace(/\\[hide\\](.*?)\\[\\/hide\\]/gis, "<span style=\\"color: #999; font-style: italic; background: #f5f5f5; padding: 2px 5px; border-radius: 3px;\\">[Hidden Content]</span>");
                
                // Replace [vip]...[/vip] and other custom tags
                content = content.replace(/\\[vip\\](.*?)\\[\\/vip\\]/gis, "<span style=\\"color: #ff9800; font-style: italic; background: #fff3e0; padding: 2px 5px; border-radius: 3px;\\">[VIP Content]</span>");
                content = content.replace(/\\[premium\\](.*?)\\[\\/premium\\]/gis, "<span style=\\"color: #9c27b0; font-style: italic; background: #f3e5f5; padding: 2px 5px; border-radius: 3px;\\">[Premium Content]</span>");
                content = content.replace(/\\[mod\\](.*?)\\[\\/mod\\]/gis, "<span style=\\"color: #f44336; font-style: italic; background: #ffebee; padding: 2px 5px; border-radius: 3px;\\">[Moderator Content]</span>");
                
                // Create a new element with the processed content
                if(content !== textNode.textContent) {
                    var span = document.createElement("span");
                    span.innerHTML = content;
                    textNode.parentNode.replaceChild(span, textNode);
                }
            });
        }
        
        // Run immediately and after a short delay
        processTextNodes();
        setTimeout(processTextNodes, 100);
    }
});
</script>';
    
    echo "<p>Add this JavaScript code to your theme's <code>headerinclude</code> template:</p>";
    echo "<textarea style='width: 100%; height: 400px; font-family: monospace;'>";
    echo htmlspecialchars($js_code);
    echo "</textarea>";
    
    echo "<div style='background: #e8f5e8; border: 1px solid #4caf50; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
    echo "<h4>✅ Instructions:</h4>";
    echo "<ol>";
    echo "<li>Go to ACP → Templates & Style → Templates → Your Theme → Header Templates → headerinclude</li>";
    echo "<li>Add the JavaScript code above at the end of the template</li>";
    echo "<li>Save the template</li>";
    echo "<li>Test search results</li>";
    echo "</ol>";
    echo "</div>";
    
    if(isset($_GET['auto_apply']))
    {
        // Try to auto-apply to headerinclude template
        $query = $db->simple_select("templates", "template, tid", "title = 'headerinclude'");
        if($db->num_rows($query) > 0)
        {
            $template = $db->fetch_array($query);
            $new_template = $template['template'] . "\n" . $js_code;
            
            $db->update_query("templates", array('template' => $db->escape_string($new_template)), "tid = '" . intval($template['tid']) . "'");
            
            echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
            echo "✅ JavaScript automatically added to headerinclude template!";
            echo "</div>";
        }
    }
    else
    {
        echo "<p><a href='?fix=javascript&auto_apply=1' style='background: #4caf50; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px;'>Auto-Apply to headerinclude</a></p>";
    }
}

echo "<p><a href='mgw_hide_debug_hooks.php'>🔍 Run Hooks Debug</a> | <a href='mgw_hide_panel.php'>← Back to MGW Hide Panel</a></p>";
?> 