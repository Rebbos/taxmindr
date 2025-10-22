# Admin System Setup Guide

## Overview
TaxMindr now supports a universal login system where both regular users and administrators use the same login page (`/public/login.php`). After authentication, users are redirected based on their role:
- **Admins** → `/admin/dashboard.php`
- **Regular Users** → `/public/dashboard.php`

## Database Schema

### New Tables Created:
1. **`admins`** - Admin-specific permissions and settings
2. **`admin_logs`** - Detailed audit trail of all admin actions
3. **`system_settings`** - System-wide configurable settings
4. **`user_reports`** - User-submitted reports/complaints for admin review

### Modified Tables:
- **`users.user_type`** - Now includes `'admin'` option
- **`tax_types.applicable_to`** - Updated to include admin
- **`tax_updates.affected_taxpayers`** - Updated to include admin

## Setup Instructions

### Step 1: Run Database Migration
```bash
# Navigate to your phpMyAdmin or MySQL client
# Import the migration file:
database/migrations/add_admin_support.sql
```

Or run via terminal:
```bash
cd c:\xampp\htdocs\taxmindr\taxmindr
mysql -u root -p taxmindr < database/migrations/add_admin_support.sql
```

### Step 2: Create First Admin User
```bash
# Navigate to the database folder
cd c:\xampp\htdocs\taxmindr\taxmindr\database

# Run the create admin script
php create_admin.php
```

**Default Credentials:**
- Email: `admin@taxmindr.com`
- Password: `Admin@123`

⚠️ **IMPORTANT:** Change the password immediately after first login!

### Step 3: Access Admin Panel
1. Go to: `http://localhost/taxmindr/taxmindr/public/login.php`
2. Login with admin credentials
3. You'll be automatically redirected to `/admin/dashboard.php`

## Admin Features

### Admin Roles
- **Super Admin** - Full system access including system settings
- **Admin** - Can manage users, tax updates, and view analytics
- **Moderator** - Limited permissions, mainly content moderation
- **Support** - User support and report management

### Admin Permissions
Each admin can have granular permissions:
- `can_manage_users` - Create, edit, suspend users
- `can_manage_tax_updates` - Post and manage tax updates/announcements
- `can_view_analytics` - Access system analytics and reports
- `can_manage_system_settings` - Modify system-wide settings (Super Admin only)

### Admin Dashboard Features
- **User Management** - View, create, edit, suspend users
- **Tax Updates** - Post BIR updates and announcements
- **User Reports** - Handle user-submitted issues
- **Analytics** - System usage statistics
- **Activity Logs** - Complete audit trail of admin actions
- **System Settings** - Configure system parameters

## Helper Functions

### Authentication
```php
isAdmin()         // Check if logged-in user is admin
requireAdmin()    // Redirect non-admins to user dashboard
```

### Admin Operations
```php
// Get admin permissions
$adminInfo = getAdminPermissions($pdo, $userId);

// Log admin activity
logAdminActivity($pdo, $adminId, 'user_created', 'Created new user John Doe', $newUserId);
```

## Security Features

1. **Universal Login** - Same login page for all user types
2. **Role-based Access** - Admins can't access user pages, users can't access admin pages
3. **Audit Trail** - All admin actions logged with IP, user agent, old/new values
4. **Permission System** - Granular control over what each admin can do
5. **Session Management** - Separate session flag for admin users

## File Structure

```
taxmindr/
├── admin/                          # Admin panel
│   ├── dashboard.php              # Admin dashboard
│   ├── users.php                  # User management (to be created)
│   ├── tax-updates.php           # Tax update management
│   └── ...
├── components/
│   ├── admin-sidebar.php         # Admin sidebar navigation
│   └── ...
├── database/
│   ├── create_admin.php          # Script to create first admin
│   └── migrations/
│       └── add_admin_support.sql # Migration script
└── public/
    ├── login.php                 # Universal login (updated with role check)
    └── dashboard.php            # Regular user dashboard
```

## Customization

### Add More Admin Pages
Create new PHP files in `/admin/` folder:

```php
<?php
require_once '../config/config.php';
requireAdmin(); // Protect the page

// Your admin page code here
?>
```

### Modify Admin Permissions
Update the `admins` table:

```sql
UPDATE admins 
SET can_manage_system_settings = TRUE 
WHERE user_id = 123;
```

### Add Custom System Settings
Insert into `system_settings` table:

```sql
INSERT INTO system_settings (setting_key, setting_value, setting_type, description)
VALUES ('feature_name', 'true', 'boolean', 'Enable/disable feature');
```

## Troubleshooting

### Issue: Can't login as admin
- Check `users` table: `user_type` should be `'admin'`
- Check `admins` table: Record should exist for the user
- Clear browser cache and cookies

### Issue: Redirected to user dashboard instead of admin
- Check session: `$_SESSION['user_type']` should be `'admin'`
- Check `requireAdmin()` function in `includes/functions.php`

### Issue: Permission denied on admin pages
- Verify admin record exists in `admins` table
- Check permission columns in admin record
- Review `requireAdmin()` implementation

## Next Steps

1. ✅ Setup complete - Admin system is ready
2. Create additional admin pages (users.php, tax-updates.php, etc.)
3. Build admin UI for managing system settings
4. Implement email notifications for user reports
5. Add export functionality for analytics
6. Create admin user management interface

## Support

For issues or questions, refer to the main TaxMindr documentation or contact the development team.
