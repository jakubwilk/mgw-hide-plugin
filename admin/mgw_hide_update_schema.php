<?php
/**
 * MGW Hide Content - Database Schema Update
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * Run this script ONCE to add custom_message column to existing installations
 * URL: http://yoursite.com/admin/mgw_hide_update_schema.php
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";
require_once "./inc/functions.php";

// Check admin permissions
if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied. You must be an administrator to run this update script.");
}

echo "<h1>🔄 MGW Hide Content - Database Schema Update</h1>";

// Check if custom_message column already exists
$table_name = $db->table_prefix . "mgw_hide_tags";
$column_exists = false;

// Get table structure
$result = $db->write_query("DESCRIBE {$table_name}");
while($row = $db->fetch_array($result))
{
    if($row['Field'] == 'custom_message')
    {
        $column_exists = true;
        break;
    }
}

if($column_exists)
{
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
    echo "<h3>✅ Already Updated</h3>";
    echo "<p>The <strong>custom_message</strong> column already exists in your database.</p>";
    echo "<p>No action needed. You can delete this script file.</p>";
    echo "</div>";
}
else
{
    echo "<h2>Adding custom_message Column</h2>";
    
    try 
    {
        // Add the custom_message column
        $db->write_query("ALTER TABLE {$table_name} ADD COLUMN custom_message TEXT NOT NULL DEFAULT '' AFTER allowed_groups");
        
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
        echo "<h3>✅ Success!</h3>";
        echo "<p>Successfully added <strong>custom_message</strong> column to the database.</p>";
        echo "<p>You can now define custom HTML messages for each hide tag!</p>";
        echo "</div>";
        
        echo "<h3>📋 Next Steps:</h3>";
        echo "<ol>";
        echo "<li>Go to <a href='mgw_hide_panel.php'>MGW Hide Content Panel</a></li>";
        echo "<li>Edit your existing tags to add custom messages</li>";
        echo "<li>Test the new functionality with a post</li>";
        echo "<li><strong>Delete this update script file for security</strong></li>";
        echo "</ol>";
    }
    catch(Exception $e)
    {
        echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
        echo "<h3>❌ Error</h3>";
        echo "<p>Failed to add custom_message column: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
}

echo "<h3>🔧 Current Table Structure:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 15px 0;'>";
echo "<tr style='background: #f8f9fa;'><th>Column</th><th>Type</th><th>Default</th></tr>";

$result = $db->write_query("DESCRIBE {$table_name}");
while($row = $db->fetch_array($result))
{
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
echo "<h4>⚠️ Security Notice</h4>";
echo "<p><strong>Delete this script file after running it successfully!</strong></p>";
echo "<p>This script should only be run once and then removed for security reasons.</p>";
echo "</div>";

echo "<p><a href='mgw_hide_panel.php'>← Back to MGW Hide Panel</a></p>";
?> 