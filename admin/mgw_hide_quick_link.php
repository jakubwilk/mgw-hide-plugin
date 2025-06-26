<?php
/**
 * MGW Hide Content - Quick Access Link
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * Include this file in admin/index.php to add MGW Hide Content quick link
 */

// Only execute if we're in the main admin panel
if(defined("IN_MYBB") && defined("IN_ADMINCP") && basename($_SERVER['PHP_SELF']) == 'index.php')
{
    // Only show for administrators and super moderators
    if($mybb->user['usergroup'] == 4 || $mybb->user['usergroup'] == 3)
    {
        // Add CSS for the quick link
        $admin_style = "
        <style>
        .mgw_hide_quick_link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            margin: 10px 0;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            font-weight: bold;
        }
        .mgw_hide_quick_link:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }
        .mgw_hide_quick_link .icon {
            font-size: 20px;
            margin-right: 10px;
        }
        </style>";
        
        // Add JavaScript to inject the link
        $admin_script = "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find a good place to insert our link (after first table or in main content)
            var container = document.querySelector('.contentTbl') || document.querySelector('table') || document.querySelector('.content');
            
            if(container) {
                var quickLink = document.createElement('a');
                quickLink.href = 'mgw_hide_panel.php';
                quickLink.className = 'mgw_hide_quick_link';
                quickLink.innerHTML = '<span class=\"icon\">🔒</span>MGW Hide Content - Manage Hide Tags';
                
                // Insert after the first table or at the beginning
                if(container.nextSibling) {
                    container.parentNode.insertBefore(quickLink, container.nextSibling);
                } else {
                    container.parentNode.appendChild(quickLink);
                }
            }
        });
        </script>";
        
        // Output the CSS and JavaScript
        echo $admin_style . $admin_script;
    }
}
?> 