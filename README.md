# MGW Hide Content Plugin for MyBB 1.8.x

**Author:** Jakub Wilk <jakub.wilk@jakubwilk.pl>  
**Version:** 1.0.19  
**Compatibility:** MyBB 1.8.x, PHP 8.1+

Advanced content hiding plugin that allows hiding post content from specific user groups using customizable BBCode tags.

## Features

- 🔒 **Multiple Hide Tags** - Create unlimited custom hide tags
- 👥 **Group Permissions** - Control which user groups can see hidden content
- 🎨 **Customizable Templates** - Edit how hidden content messages look
- ⚡ **Performance Optimized** - Efficient parsing and caching
- 🌍 **Multi-language Ready** - Easy translation support
- 📱 **Responsive Design** - Works on all devices

## Installation

1. **Upload Files:**
   ```
   inc/plugins/mgw_hide.php
   admin/mgw_hide_panel.php
   admin/language/english/config_mgw_hide.lang.php
   mgw_hide.css (optional)
   ```

2. **Install Plugin:**
   - Go to Admin CP → Configuration → Plugins
   - Find "MGW Hide Content" and click **Install & Activate**

3. **Access Admin Panel:**
   - Go to `http://yoursite.com/admin/mgw_hide_panel.php`
   - Or use direct link from Admin CP
   - Optional: Add to ACP navigation (see Navigation Integration below)

## Admin Panel

### 🔧 Access Methods

**Method 1 - Direct URL:**
```
http://yoursite.com/admin/mgw_hide_panel.php
```

**Method 2 - Bookmark:** Add to your browser bookmarks for quick access

### 🔗 Navigation Integration (Optional)

To add MGW Hide Content to your ACP navigation menu:

**Option 1 - Quick Link on Dashboard:**
1. Edit your `admin/index.php` file
2. Add this line before the closing `</body>` tag:
   ```php
   <?php include_once "mgw_hide_quick_link.php"; ?>
   ```
3. Save and refresh your ACP dashboard

**Option 2 - Manual Menu Addition:**
Add a bookmark or create a custom menu entry pointing to `mgw_hide_panel.php`

### 🏷️ Managing Hide Tags

1. **Default Tag:** `[hide]` - Works for Administrators and Super Moderators
2. **Add Custom Tags:** Create tags like `[vip]`, `[premium]`, `[mod]`
3. **Group Permissions:** Select which user groups can see the content
4. **Tag Status:** Enable/disable tags without deleting them

### Example Usage

```bbcode
This content is visible to everyone.

[hide]This content is hidden from guests and regular users.[/hide]

[vip]This content is only visible to VIP members.[/vip]

[mod]Moderator-only information here.[/mod]
```

## Settings

Access plugin settings in:
**Admin CP → Configuration → Settings → MGW Hide Content Settings**

Available settings:
- **Enable/Disable Plugin**
- **Hidden Content Message** - Text shown to users who can't see content
- **Author Always Sees** - Whether post authors can always see their hidden content

## User Groups

Default MyBB user groups:
- **1:** Guests
- **2:** Registered Users  
- **3:** Super Moderators
- **4:** Administrators
- **5:** Awaiting Activation
- **6:** Moderators

## Features in Detail

### 🔒 Content Hiding
- Content hidden during post display
- Hidden during quote/reply (preserves tags for editing)
- Hidden in search results
- Hidden for RSS feeds

### 🎨 Template System
Built-in templates:
- `mgw_hide_message` - Message for logged-in users
- `mgw_hide_message_guest` - Message for guests
- `mgw_hide_visible_content` - Wrapper for visible content

### 🔧 MyCode Integration
- Automatically creates MyCode entries for each tag
- Proper BBCode parsing and validation
- Integration with MyBB's parser system

## Troubleshooting

### Can't Access Admin Panel?
- Ensure you're logged in as Administrator (group 4) or Super Moderator (group 3)
- Try direct URL: `http://yoursite.com/admin/mgw_hide_panel.php`
- Check file permissions

### Tags Showing Strange Text?
If you see `[HANDLED BY MGW HIDE PLUGIN]` in posts:
1. Run cleanup script: `http://yoursite.com/admin/mgw_hide_cleanup.php`
2. Or manually delete problematic MyCode entries from ACP
3. Plugin now handles parsing directly without MyCode conflicts

### Tags Not Working?
- Verify plugin is **activated**
- Check tag is **active** in admin panel
- Ensure correct group permissions are set
- Clear MyBB cache
- Run cleanup script if upgrading from older version

### Permission Issues?
- Only Administrators and Super Moderators can access the panel
- Plugin creates proper database permissions during activation

## Security

- Admin panel requires proper MyBB authentication
- All user input is escaped and validated
- SQL injection protection
- XSS protection for all outputs

## Files Structure

```
mgw_hide/
├── inc/plugins/mgw_hide.php                    # Main plugin file
├── admin/mgw_hide_panel.php                    # Admin panel
├── admin/language/english/config_mgw_hide.lang.php  # Language file
├── mgw_hide.css                                # Optional CSS styles
├── README.md                                   # This file
├── INSTALL.md                                  # Installation guide
├── TEMPLATES.md                               # Template documentation
└── LICENSE                                    # License file
```

## License

This plugin is released under the MIT License. See LICENSE file for details.

## Support

For support, bug reports, or feature requests:
- **Email:** jakub.wilk@jakubwilk.pl
- **Website:** https://jakubwilk.pl

## Changelog

### 1.0.19 (Current)
- 🔧 Fixed hide tags visible in search results
- ✅ Added search_results_postbit hook for better coverage
- ✅ Improved search results processing logic
- ✅ Added live search test script for debugging
- ✅ Enhanced search results validation

### 1.0.18
- 🐛 Fixed TypeError in search results hook 
- ✅ Added input validation for search_results_post hook
- ✅ Improved error handling for non-array parameters
- ✅ Added debug test script for search functionality

### 1.0.17
- ✅ Added custom HTML messages per tag
- ✅ New custom_message field in database
- ✅ Enhanced admin panel with custom message editor
- ✅ Full backward compatibility
- ✅ Database schema update script
- ✅ Rich HTML message examples and documentation

### 1.0.8
- ✅ Fixed MyCode parsing conflicts
- ✅ Eliminated double-parsing issues
- ✅ Simplified parser for better reliability
- ✅ Added cleanup script for existing installations

### 1.0.6-1.0.7
- ✅ Fixed admin panel access issues
- ✅ Created autonomous admin panel
- ✅ Improved permission system
- ✅ Better error handling

### 1.0.5
- ✅ Enhanced admin permissions system
- ✅ Added diagnostic tools
- ✅ Bug fixes for template system

### 1.0.0 - 1.0.4
- ✅ Initial release
- ✅ Core functionality
- ✅ Basic admin interface
- ✅ Template system 