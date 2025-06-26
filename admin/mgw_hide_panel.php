<?php
/**
 * MGW Hide Content - Autonomiczny Panel Administracyjny
 * Author: Jakub Wilk <jakub.wilk@jakubwilk.pl>
 * 
 * Użyj: http://twoja-domena.pl/admin/mgw_hide_panel.php
 */

define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../global.php";
require_once "./inc/functions.php";

// Check admin permissions
if($mybb->user['usergroup'] != 4 && $mybb->user['usergroup'] != 3)
{
    die("Access denied. You must be an administrator to access this panel.");
}

// Include plugin functions
if(file_exists("../inc/plugins/mgw_hide.php"))
{
    include_once "../inc/plugins/mgw_hide.php";
}

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : 'tags';

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if($_POST['action_type'] == 'add_tag')
    {
        $tag_name = trim($_POST['tag_name']);
        $tag_description = trim($_POST['tag_description']);
        $allowed_groups = isset($_POST['allowed_groups']) ? $_POST['allowed_groups'] : array();
        
        if(empty($tag_name))
        {
            $error = "Tag name is required.";
        }
        else
        {
            // Check if tag exists
            $existing = $db->fetch_field($db->simple_select("mgw_hide_tags", "id", "tag_name = '" . $db->escape_string($tag_name) . "'"), "id");
            if($existing)
            {
                $error = "A tag with this name already exists.";
            }
            else
            {
                // Insert new tag
                $new_tag = array(
                    'tag_name' => $db->escape_string($tag_name),
                    'tag_description' => $db->escape_string($tag_description),
                    'allowed_groups' => $db->escape_string(implode(',', array_filter($allowed_groups))),
                    'is_active' => 1,
                    'created_at' => time(),
                    'updated_at' => time()
                );
                
                $db->insert_query("mgw_hide_tags", $new_tag);
                
                // MyCode no longer used - we handle parsing with hooks only
                // if(function_exists('mgw_hide_update_mycodes'))
                // {
                //     mgw_hide_update_mycodes();
                // }
                
                $success = "Tag created successfully.";
            }
        }
    }
    elseif($_POST['action_type'] == 'edit_tag')
    {
        $tag_id = intval($_POST['tag_id']);
        $tag_description = trim($_POST['tag_description']);
        $allowed_groups = isset($_POST['allowed_groups']) ? $_POST['allowed_groups'] : array();
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $update_array = array(
            'tag_description' => $db->escape_string($tag_description),
            'allowed_groups' => $db->escape_string(implode(',', array_filter($allowed_groups))),
            'is_active' => $is_active,
            'updated_at' => time()
        );
        
        $db->update_query("mgw_hide_tags", $update_array, "id = '" . $tag_id . "'");
        
        // MyCode no longer used - we handle parsing with hooks only
        // if(function_exists('mgw_hide_update_mycodes'))
        // {
        //     mgw_hide_update_mycodes();
        // }
        
        $success = "Tag updated successfully.";
    }
    elseif($_POST['action_type'] == 'delete_tag')
    {
        $tag_id = intval($_POST['tag_id']);
        
        // Check if it's default tag
        $tag = $db->fetch_array($db->simple_select("mgw_hide_tags", "*", "id = '" . $tag_id . "'"));
        if($tag['tag_name'] == 'hide')
        {
            $error = "Cannot delete the default 'hide' tag.";
        }
        else
        {
            $db->delete_query("mgw_hide_tags", "id = '" . $tag_id . "'");
            
            // MyCode no longer used - we handle parsing with hooks only
            // if(function_exists('mgw_hide_update_mycodes'))
            // {
            //     mgw_hide_update_mycodes();
            // }
            
            $success = "Tag deleted successfully.";
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>MGW Hide Content - Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .nav { border-bottom: 1px solid #ddd; margin-bottom: 20px; }
        .nav a { display: inline-block; padding: 10px 15px; text-decoration: none; color: #333; border-bottom: 2px solid transparent; }
        .nav a.active, .nav a:hover { border-bottom-color: #007cba; color: #007cba; }
        .alert { padding: 10px; margin: 10px 0; border-radius: 3px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
        .form-group select[multiple] { height: 120px; }
        .btn { padding: 8px 15px; background: #007cba; color: white; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #005a87; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #a71e2a; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background: #f8f9fa; font-weight: bold; }
        .tag-name { font-family: monospace; font-weight: bold; color: #007cba; }
        .status-active { color: #28a745; font-weight: bold; }
        .status-inactive { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔒 MGW Hide Content - Admin Panel</h1>
        
        <div class="nav">
            <a href="?action=tags" class="<?php echo $action == 'tags' ? 'active' : ''; ?>">Manage Tags</a>
            <a href="?action=settings" class="<?php echo $action == 'settings' ? 'active' : ''; ?>">Settings</a>
            <a href="index.php">← Back to ACP</a>
            <a href="index.php?module=config-settings&action=change&search=mgw_hide">Plugin Settings</a>
        </div>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if($action == 'tags'): ?>
            <h2>Add New Hide Tag</h2>
            <form method="post">
                <input type="hidden" name="action_type" value="add_tag">
                
                <div class="form-group">
                    <label>Tag Name (without brackets)</label>
                    <input type="text" name="tag_name" placeholder="e.g., vip, premium, mod" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="tag_description" placeholder="Brief description of this tag's purpose"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Allowed Groups (hold Ctrl/Cmd to select multiple)</label>
                    <select name="allowed_groups[]" multiple>
                        <?php
                        $query = $db->simple_select("usergroups", "gid, title", "", array("order_by" => "title"));
                        while($group = $db->fetch_array($query))
                        {
                            $selected = in_array($group['gid'], array(3, 4)) ? 'selected' : '';
                            echo "<option value='{$group['gid']}' {$selected}>{$group['title']} (ID: {$group['gid']})</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit" class="btn">Add Tag</button>
            </form>
            
            <h2>Existing Tags</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tag Name</th>
                        <th>Description</th>
                        <th>Allowed Groups</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = $db->simple_select("mgw_hide_tags", "*", "", array("order_by" => "tag_name"));
                    while($tag = $db->fetch_array($query))
                    {
                        echo "<tr>";
                        echo "<td class='tag-name'>[{$tag['tag_name']}]</td>";
                        echo "<td>" . htmlspecialchars($tag['tag_description']) . "</td>";
                        
                        // Get group names
                        $group_names = array();
                        if($tag['allowed_groups'])
                        {
                            $groups = explode(',', $tag['allowed_groups']);
                            foreach($groups as $gid)
                            {
                                $group = $db->fetch_array($db->simple_select("usergroups", "title", "gid = '" . intval($gid) . "'"));
                                if($group)
                                {
                                    $group_names[] = $group['title'];
                                }
                            }
                        }
                        echo "<td>" . implode(', ', $group_names) . "</td>";
                        
                        $status_class = $tag['is_active'] ? 'status-active' : 'status-inactive';
                        $status_text = $tag['is_active'] ? 'Active' : 'Inactive';
                        echo "<td class='{$status_class}'>{$status_text}</td>";
                        
                        echo "<td>";
                        echo "<a href='?action=edit&id={$tag['id']}' class='btn'>Edit</a> ";
                        if($tag['tag_name'] != 'hide')
                        {
                            echo "<a href='javascript:void(0);' onclick='deleteTag({$tag['id']})' class='btn btn-danger'>Delete</a>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            
        <?php elseif($action == 'edit'): ?>
            <?php
            $tag_id = intval($_GET['id']);
            $tag = $db->fetch_array($db->simple_select("mgw_hide_tags", "*", "id = '" . $tag_id . "'"));
            if(!$tag) die("Tag not found.");
            ?>
            
            <h2>Edit Tag: [<?php echo htmlspecialchars($tag['tag_name']); ?>]</h2>
            <form method="post">
                <input type="hidden" name="action_type" value="edit_tag">
                <input type="hidden" name="tag_id" value="<?php echo $tag['id']; ?>">
                
                <div class="form-group">
                    <label>Tag Name (cannot be changed)</label>
                    <input type="text" value="[<?php echo htmlspecialchars($tag['tag_name']); ?>]" disabled>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="tag_description"><?php echo htmlspecialchars($tag['tag_description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Allowed Groups</label>
                    <select name="allowed_groups[]" multiple>
                        <?php
                        $selected_groups = explode(',', $tag['allowed_groups']);
                        $query = $db->simple_select("usergroups", "gid, title", "", array("order_by" => "title"));
                        while($group = $db->fetch_array($query))
                        {
                            $selected = in_array($group['gid'], $selected_groups) ? 'selected' : '';
                            echo "<option value='{$group['gid']}' {$selected}>{$group['title']} (ID: {$group['gid']})</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" <?php echo $tag['is_active'] ? 'checked' : ''; ?>>
                        Active
                    </label>
                </div>
                
                <button type="submit" class="btn">Update Tag</button>
                <a href="?action=tags" class="btn">Cancel</a>
            </form>
            
        <?php elseif($action == 'settings'): ?>
            <h2>Plugin Settings</h2>
            <p>Plugin settings can be managed in <a href="index.php?module=config-settings&action=change&search=mgw_hide">Configuration → Settings</a>.</p>
            
            <h3>Current Settings</h3>
            <table>
                <tr><td><strong>Plugin Status</strong></td><td><?php echo $mybb->settings['mgw_hide_enabled'] ? '<span class="status-active">Enabled</span>' : '<span class="status-inactive">Disabled</span>'; ?></td></tr>
                <tr><td><strong>Hidden Content Message</strong></td><td><?php echo htmlspecialchars($mybb->settings['mgw_hide_show_message']); ?></td></tr>
                <tr><td><strong>Author Always Sees</strong></td><td><?php echo $mybb->settings['mgw_hide_author_always_see'] ? 'Yes' : 'No'; ?></td></tr>
            </table>
            
            <h3>Usage Example</h3>
            <p>After creating tags, use them in posts like this:</p>
            <pre style="background: #f8f9fa; padding: 10px; border-left: 4px solid #007cba;">
Public content visible to everyone.

[hide]This content is hidden from guests and regular users.[/hide]

[vip]This content is only visible to VIP members.[/vip]
            </pre>
            
        <?php endif; ?>
    </div>
    
    <script>
    function deleteTag(id) {
        if(confirm("Are you sure you want to delete this tag?")) {
            var form = document.createElement("form");
            form.method = "post";
            
            var actionType = document.createElement("input");
            actionType.type = "hidden";
            actionType.name = "action_type";
            actionType.value = "delete_tag";
            form.appendChild(actionType);
            
            var tagId = document.createElement("input");
            tagId.type = "hidden";
            tagId.name = "tag_id";
            tagId.value = id;
            form.appendChild(tagId);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html> 