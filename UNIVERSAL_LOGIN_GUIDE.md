# Universal Login with Separate Tables - Quick Reference

## Architecture Overview

### Two Separate Tables:
```
┌─────────────┐                    ┌─────────────┐
│   ADMINS    │                    │    USERS    │
├─────────────┤                    ├─────────────┤
│ admin_id    │                    │ user_id     │
│ email       │                    │ email       │
│ password    │                    │ password    │
│ role        │                    │ user_type   │
│ permissions │                    │ tin         │
└─────────────┘                    └─────────────┘
```

### Login Flow:
```
User visits: /public/login.php
        ↓
Enters email + password
        ↓
System checks ADMINS table first
        ├─ Found? → Admin login → /admin/dashboard.php
        └─ Not found → Check USERS table
                ├─ Found? → User login → /public/dashboard.php
                └─ Not found → Invalid credentials
```

## Key Differences from Before

### ❌ Old Approach (Single Table):
- One `users` table with `user_type` column
- Admins were just users with `user_type = 'admin'`
- Required foreign keys between users and admins tables

### ✅ New Approach (Separate Tables):
- **`admins`** table - Completely independent admin accounts
- **`users`** table - Only regular users (no admins)
- No foreign keys between them
- Cleaner separation of concerns

## Session Variables

### For Admins:
```php
$_SESSION['admin_id']      // Admin's unique ID
$_SESSION['admin_role']    // super_admin, admin, moderator, support
$_SESSION['is_admin']      // true
$_SESSION['account_type']  // 'admin'
$_SESSION['email']
$_SESSION['first_name']
$_SESSION['last_name']
```

### For Regular Users:
```php
$_SESSION['user_id']       // User's unique ID
$_SESSION['user_type']     // individual, business, freelancer, organization
$_SESSION['is_admin']      // false
$_SESSION['account_type']  // 'user'
$_SESSION['email']
$_SESSION['first_name']
$_SESSION['last_name']
```

## Access Control Functions

```php
// Check if anyone is logged in (admin OR user)
isLoggedIn()           // Returns true if admin or user is logged in

// Check if current account is admin
isAdmin()              // Returns true only for admins

// Protect admin pages
requireAdmin()         // Redirects non-admins to user dashboard or login

// Protect user pages
requireUser()          // Redirects admins to admin dashboard

// Generic login requirement
requireLogin()         // Redirects to login if not authenticated
```

## Usage Examples

### Protecting Admin Pages:
```php
<?php
require_once '../config/config.php';
requireAdmin(); // Only admins can access this page

$adminId = $_SESSION['admin_id'];
// Admin-only logic here
?>
```

### Protecting User Pages:
```php
<?php
require_once '../config/config.php';
requireUser(); // Only regular users can access, not admins

$userId = $_SESSION['user_id'];
// User-only logic here
?>
```

### Universal Check:
```php
<?php
require_once '../config/config.php';
requireLogin(); // Anyone logged in (admin or user)

if (isAdmin()) {
    // Show admin content
} else {
    // Show user content
}
?>
```

## Setup Steps

1. **Run migration:**
   ```bash
   mysql -u root taxmindr < database/migrations/add_admin_support.sql
   ```

2. **Create first admin:**
   ```bash
   cd database
   php create_admin.php
   ```

3. **Login:**
   - Go to: `http://localhost/taxmindr/taxmindr/public/login.php`
   - Email: `admin@taxmindr.com`
   - Password: `Admin@123`
   - Automatically redirected to `/admin/dashboard.php`

## Benefits of Separate Tables

✅ **Complete Isolation** - Admins and users never mix  
✅ **Cleaner Schema** - No user_type confusion  
✅ **Better Security** - No accidental admin privilege escalation  
✅ **Easier Queries** - Don't need to filter by user_type  
✅ **Scalability** - Can add admin-specific fields without affecting users table  

## Login Page Logic

The login page (`/public/login.php`) implements this logic:

```php
// Check admins table first
$admin = query("SELECT * FROM admins WHERE email = ?", [$email]);

if (!$admin) {
    // Not an admin, check users table
    $user = query("SELECT * FROM users WHERE email = ?", [$email]);
}

if (password_verify($password, $account['password_hash'])) {
    if ($account_type === 'admin') {
        $_SESSION['admin_id'] = $admin_id;
        $_SESSION['is_admin'] = true;
        redirect('/admin/dashboard.php');
    } else {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['is_admin'] = false;
        redirect('/public/dashboard.php');
    }
}
```

## File Structure

```
taxmindr/
├── admin/                    # Admin-only pages
│   ├── dashboard.php        # Admin dashboard
│   ├── users.php            # Manage users
│   └── ...
├── public/                   # User pages + login
│   ├── login.php            # Universal login (checks both tables)
│   ├── dashboard.php        # User dashboard
│   └── ...
├── components/
│   ├── admin-sidebar.php    # Admin navigation
│   └── sidebar.php          # User navigation
└── includes/
    └── functions.php        # Auth helpers (isAdmin, requireUser, etc.)
```

---

**Remember:** Admins and users are completely separate. An admin account is NOT a user account and vice versa!
