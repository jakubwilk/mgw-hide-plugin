# MGW Hide Content - Installation Guide

## Quick Installation

### Step 1: Upload Files
Upload these files to your MyBB installation:

```
📁 Your MyBB Root Directory/
├── inc/plugins/mgw_hide.php
├── admin/mgw_hide_panel.php
├── admin/mgw_hide_quick_link.php (optional)
├── admin/language/english/config_mgw_hide.lang.php
└── mgw_hide.css (optional)
```

### Step 2: Install Plugin
1. Login to your **Admin Control Panel**
2. Go to **Configuration → Plugins**
3. Find **"MGW Hide Content"** in the list
4. Click **"Install & Activate"**

### Step 3: Access Management Panel
**Method 1 - Direct URL:**
```
http://yoursite.com/admin/mgw_hide_panel.php
```

**Method 2 - Add to ACP Navigation (Optional):**
1. Edit `admin/index.php`
2. Add before `</body>`:
   ```php
   <?php include_once "mgw_hide_quick_link.php"; ?>
   ```
3. Save and refresh ACP

## Post-Installation Setup

### 1. Configure Basic Settings
- Go to **Configuration → Settings → MGW Hide Content**
- Enable the plugin
- Set default hidden content message
- Configure author permissions

### 2. Create Custom Hide Tags
1. Open **MGW Hide Content Panel**
2. Go to **"Manage Tags"** tab
3. Add new tags (e.g., `vip`, `premium`, `mod`)
4. Select which user groups can see each tag
5. Test the tags in forum posts

### 3. Test Functionality
Create a test post with:
```bbcode
Public content visible to everyone.

[hide]This content is hidden from guests.[/hide]

[vip]This content is only for VIP members.[/vip]
```

## File Permissions

Ensure these files have proper permissions:
- `inc/plugins/mgw_hide.php` - 644
- `admin/mgw_hide_panel.php` - 644
- `admin/mgw_hide_quick_link.php` - 644

## Database Tables

The plugin creates this table:
- `{prefix}mgw_hide_tags` - Stores custom hide tags and permissions

## Verification Checklist

✅ Plugin installed and activated  
✅ Admin panel accessible  
✅ Default `[hide]` tag working  
✅ Custom tags can be created  
✅ Group permissions working  
✅ Hidden content displays properly  

## Troubleshooting

### Can't Access Admin Panel?
- Verify you're logged in as Administrator (group 4) or Super Moderator (group 3)
- Check file permissions (644)
- Try direct URL: `http://yoursite.com/admin/mgw_hide_panel.php`

### Plugin Not Working?
- Ensure plugin is **activated** (not just installed)
- Check MyBB error logs
- Verify database table was created
- Clear MyBB cache

### Permission Issues?
- Only Administrators and Super Moderators can access the panel
- Regular users should see hidden content messages
- Authors can see their own hidden content (if enabled in settings)

### Tags Not Hiding Content?
- Verify tag is **active** in admin panel
- Check group permissions are set correctly
- Ensure MyBB cache is cleared
- Test with simple `[hide]` tag first

## Manual Installation (Alternative)

If automatic installation fails:

1. **Create Database Table:**
   ```sql
   CREATE TABLE `mybb_mgw_hide_tags` (
     `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
     `tag_name` varchar(50) NOT NULL,
     `tag_description` varchar(255) NOT NULL DEFAULT '',
     `allowed_groups` text NOT NULL,
     `is_active` tinyint(1) NOT NULL DEFAULT 1,
     `created_at` int(10) unsigned NOT NULL,
     `updated_at` int(10) unsigned NOT NULL,
     PRIMARY KEY (`id`),
     UNIQUE KEY `tag_name` (`tag_name`)
   ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   ```

2. **Insert Default Tag:**
   ```sql
   INSERT INTO `mybb_mgw_hide_tags` VALUES 
   (1, 'hide', 'Default hide tag', '3,4', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
   ```

3. **Add Plugin Settings:** Go to ACP and add the plugin manually

## Uninstallation

To completely remove the plugin:

1. **Deactivate Plugin:** ACP → Configuration → Plugins → MGW Hide Content → Deactivate
2. **Uninstall Plugin:** Click "Uninstall" 
3. **Remove Files:**
   - `inc/plugins/mgw_hide.php`
   - `admin/mgw_hide_panel.php`
   - `admin/mgw_hide_quick_link.php`
   - `admin/language/english/config_mgw_hide.lang.php`
4. **Remove Navigation (if added):** Remove include line from `admin/index.php`

The plugin will automatically clean up database tables and settings during uninstallation.

## Support

Need help? Contact:
- **Email:** jakub.wilk@jakubwilk.pl
- **Website:** https://jakubwilk.pl 