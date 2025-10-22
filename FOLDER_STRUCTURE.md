# TaxMindr - Project Structure

## 📁 Folder Organization

```
taxmindr/
├── index.php                    # Entry point (redirects to /landing)
│
├── landing/                     # 🌐 LANDING PAGE (Public Marketing Site)
│   ├── index.php               # Homepage
│   ├── about.php               # About page
│   ├── features.php            # Features page
│   └── assets/                 # Landing page assets
│       ├── css/
│       ├── js/
│       └── images/
│
├── admin/                       # 👨‍💼 ADMIN PANEL (Admins Only)
│   ├── dashboard.php           # Admin dashboard
│   ├── users.php               # User management (to be created)
│   ├── tax-updates.php         # Tax updates management
│   ├── reports.php             # User reports
│   ├── analytics.php           # Analytics & statistics
│   ├── activity-logs.php       # Admin activity logs
│   ├── settings.php            # System settings
│   └── assets/                 # Admin-specific assets
│       ├── css/
│       ├── js/
│       └── images/
│
├── public/                      # 👤 USER AREA (Regular Users)
│   ├── login.php               # Universal login (admin + user)
│   ├── register.php            # User registration
│   ├── logout.php              # Logout
│   ├── dashboard.php           # User dashboard
│   ├── deadlines.php           # Tax deadlines
│   ├── upload_withholding.php  # Upload withholding lists
│   ├── updates.php             # Tax updates
│   ├── submissions.php         # Filing archive
│   └── settings.php            # User settings
│
├── components/                  # 🧩 REUSABLE COMPONENTS
│   ├── head.php                # HTML head
│   ├── foot.php                # Scripts
│   ├── topbar.php              # Accent bar
│   ├── page-header.php         # Internal page header
│   ├── page-footer.php         # Page footer
│   ├── sidebar.php             # User sidebar
│   ├── admin-sidebar.php       # Admin sidebar
│   └── breadcrumb.php          # Breadcrumb component
│
├── config/                      # ⚙️ CONFIGURATION
│   ├── config.php              # Main configuration
│   └── database.php            # Database connection
│
├── includes/                    # 📦 SHARED CODE
│   └── functions.php           # Helper functions
│
├── assets/                      # 🎨 SHARED ASSETS
│   ├── css/
│   │   ├── modern-style.css    # Main stylesheet
│   │   ├── style.css           # Legacy styles
│   │   └── forms.css           # Form styles
│   ├── js/
│   │   ├── modern-app.js       # Main JavaScript
│   │   └── main.js             # Legacy JS
│   └── images/                 # Shared images
│
├── database/                    # 💾 DATABASE
│   ├── schema.sql              # Complete database schema
│   ├── create_admin.php        # Create first admin script
│   └── migrations/
│       └── add_admin_support.sql
│
└── uploads/                     # 📤 USER UPLOADS
    └── (user uploaded files)
```

## 🎯 Access Control

### Landing Page (`/landing`)
- **Access:** Public (anyone)
- **Purpose:** Marketing, information, getting started
- **Login Required:** No

### Admin Panel (`/admin`)
- **Access:** Admins only (`admins` table)
- **Login:** Via `/public/login.php` (universal)
- **Protected by:** `requireAdmin()` function
- **Redirect:** Non-admins redirected to user dashboard

### User Area (`/public`)
- **Access:** Regular users only (`users` table)
- **Login:** Via `/public/login.php` (universal)
- **Protected by:** `requireUser()` function (blocks admins!)
- **Redirect:** Admins redirected to admin dashboard

## 🔐 Authentication Flow

```
User visits: /public/login.php
    │
    ├─→ Admin credentials?
    │   └─→ Redirect to /admin/dashboard.php
    │
    └─→ User credentials?
        └─→ Redirect to /public/dashboard.php
```

## 📂 Assets Organization

### Shared Assets (`/assets`)
Used by both admin and user areas:
- Bootstrap CSS/JS
- Common utilities
- Shared images

### Landing Assets (`/landing/assets`)
Marketing site specific:
- Landing page styles
- Hero images
- Feature graphics

### Admin Assets (`/admin/assets`)
Admin panel specific:
- Admin-only styles
- Admin dashboard charts
- Admin icons

## 🚀 URL Structure

| Type | URL | Access |
|------|-----|--------|
| **Landing** | `/landing/` | Public |
| **Login** | `/public/login.php` | Public (Universal) |
| **Register** | `/public/register.php` | Public |
| **User Dashboard** | `/public/dashboard.php` | Users only |
| **Admin Dashboard** | `/admin/dashboard.php` | Admins only |

## 📝 Best Practices

### When Creating New Pages:

**Landing Page:**
```php
// In /landing folder
require_once '../config/config.php';
// Public content - no authentication
```

**Admin Page:**
```php
// In /admin folder
require_once '../config/config.php';
requireAdmin(); // Protect admin pages
$adminId = $_SESSION['admin_id'];
```

**User Page:**
```php
// In /public folder
require_once '../config/config.php';
requireUser(); // Protect user pages (blocks admins too!)
$userId = $_SESSION['user_id'];
```

## 🔧 Configuration

All paths configured in `/config/config.php`:
```php
define('APP_URL', 'http://localhost/taxmindr/taxmindr');
```

Access URLs:
- Landing: `APP_URL . '/landing/'`
- Admin: `APP_URL . '/admin/'`
- User: `APP_URL . '/public/'`

## 📌 Quick Links

- **Entry Point:** `http://localhost/taxmindr/taxmindr/`
- **Landing:** `http://localhost/taxmindr/taxmindr/landing/`
- **Login:** `http://localhost/taxmindr/taxmindr/public/login.php`
- **Admin:** `http://localhost/taxmindr/taxmindr/admin/dashboard.php`
- **User:** `http://localhost/taxmindr/taxmindr/public/dashboard.php`

---

**Remember:** 
- Landing = Public marketing
- Admin = Admins only (separate table)
- Public = Users only (separate table)
- Login = Universal for both
