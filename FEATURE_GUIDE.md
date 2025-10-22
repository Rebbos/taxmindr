# TaxMindr - Complete Feature Guide

## 🎨 New UI Features

### Burger Menu & Sidebar Toggle

#### How to Use:
1. **Click the burger menu (☰)** in the top-left corner of the navbar
2. **Desktop behavior**: Sidebar toggles between full (280px) and mini (80px) mode
3. **Mobile behavior**: Sidebar slides in from left as overlay
4. **Persistent memory**: Your choice is saved and remembered across all pages

#### What Changes:
- **Full sidebar (280px)**: Shows icons, labels, and user info
- **Mini sidebar (80px)**: Shows only icons, perfect for more screen space
- **Main content area**: Automatically adjusts to give you more workspace

### Navigation Structure

```
📱 Navbar (Top)
   ├── 🍔 Burger Menu (toggles sidebar)
   ├── 🏠 TaxMindr Logo
   └── 👤 User Dropdown
       ├── Profile
       ├── Settings
       └── Logout

📋 Sidebar (Left)
   ├── 👤 User Card (Name, Type)
   ├── 📊 Dashboard
   ├── 📅 Deadlines (with badge count)
   ├── 📊 Upload Withholding
   ├── 📰 Tax Updates
   ├── 📁 Filing Archive
   └── ⚙️ Settings
```

## 📱 Responsive Design

### Breakpoints:
- **Desktop**: ≥992px - Full sidebar + main content side-by-side
- **Tablet**: 768px - 991px - Collapsible sidebar with overlay
- **Mobile**: <768px - Overlay sidebar, stacked content

### Mobile Features:
- Burger menu shows sidebar overlay
- Tap outside sidebar to close
- Touch-friendly button sizes
- Responsive tables scroll horizontally
- Cards stack vertically

## 🎯 Page-by-Page Features

### 1. Dashboard (`dashboard.php`)
**Features:**
- ✅ Statistics cards (Pending, Upcoming, Filed, Overdue)
- ✅ Upcoming deadlines list with countdown badges
- ✅ Recent tax updates sidebar
- ✅ Clean interface (removed duplicate Quick Actions)

**What You See:**
- 📊 4 stat cards at the top
- 📅 List of upcoming deadlines (left)
- 📰 Recent tax updates (right)

### 2. Deadlines (`deadlines.php`)
**Features:**
- ✅ Filter by period (Upcoming, Overdue, This Month, All)
- ✅ Filter by status (Pending, Filed, Paid, Late)
- ✅ Responsive table with deadline info
- ✅ Color-coded urgency badges
- ✅ Quick action buttons (Mark Filed, Edit)

**Badge Colors:**
- 🟢 Green: 8+ days left
- 🟡 Yellow: 4-7 days left
- 🔴 Red: 0-3 days left or overdue

### 3. Upload Withholding (`upload_withholding.php`)
**Features:**
- ✅ Drag & drop file upload
- ✅ Support for CSV, XLSX, XLS
- ✅ Filing period selection
- ✅ Tax type dropdown
- ✅ Recent uploads history

**Supported Formats:**
- `.csv` - Comma-separated values
- `.xlsx` - Excel 2007+
- `.xls` - Excel 97-2003

### 4. Tax Updates (`updates.php`)
**Features:**
- ✅ All BIR announcements and updates
- ✅ Filter by relevance
- ✅ Action required indicator
- ✅ Detailed update descriptions
- ✅ Publication dates

**Filter Options:**
- All Updates
- Relevant to Me
- Action Required

### 5. Filing Archive (`submissions.php`)
**Features:**
- ✅ Complete history of filed returns
- ✅ Tax type and BIR form details
- ✅ Filing period tracking
- ✅ Submission dates
- ✅ Amount paid records
- ✅ Status indicators

### 6. Settings (`settings.php`)
**Features:**
- ✅ Profile information update
- ✅ TIN and mobile number management
- ✅ Business name (if applicable)
- ✅ Reminder preferences
  - Days before deadline
  - Email notifications
  - SMS notifications
  - Reminder time
- ✅ Password change

## 🔧 Technical Implementation

### Component System
All pages now use modular components:

```php
// Template for any new page
<?php
require_once '../config/config.php';
requireLogin();

$pageTitle = 'Your Page - TaxMindr';
include '../components/head.php';
?>

<body>
    <?php include '../components/navbar.php'; ?>
    
    <div class="d-flex">
        <?php include '../components/sidebar.php'; ?>
        
        <main class="main-wrapper flex-grow-1">
            <h1>Your Page Title</h1>
            <!-- Your content here -->
        </main>
    </div>
    
    <?php include '../components/foot.php'; ?>
```

### JavaScript Functions

**Available globally via `TaxMindr` object:**
```javascript
// Show toast notification
TaxMindr.showToast('Success!', 'success'); // or 'error', 'warning', 'info'

// Format TIN
TaxMindr.formatTIN('123456789012'); // Returns: 123-456-789-012

// Format mobile
TaxMindr.formatMobile('09171234567'); // Returns: 0917-123-4567

// Copy to clipboard
TaxMindr.copyToClipboard('text to copy');
```

### CSS Classes

**Utility Classes:**
```html
<!-- Cards -->
<div class="card">
    <div class="card-header">Header</div>
    <div class="card-body">Content</div>
</div>

<!-- Statistics -->
<div class="stat-card stat-primary">
    <div class="stat-icon"><i class="bi bi-icon"></i></div>
    <h3>123</h3>
    <p>Description</p>
</div>

<!-- Badges -->
<span class="badge bg-success">Success</span>
<span class="badge bg-danger">Danger</span>
<span class="badge bg-warning text-dark">Warning</span>

<!-- Buttons -->
<button class="btn btn-primary">Primary</button>
<button class="btn btn-outline-secondary">Secondary</button>
```

## 🚀 Performance Features

### Fast Loading
- ✅ CDN-hosted Bootstrap 5 (cached by browser)
- ✅ Optimized CSS (500 lines, minified)
- ✅ Minimal JavaScript (only what's needed)
- ✅ No jQuery dependency

### Smart Features
- ✅ LocalStorage for sidebar state (persists across sessions)
- ✅ Auto-format TIN and mobile inputs
- ✅ Countdown timers update every 60 seconds
- ✅ Form validation before submission
- ✅ Auto-dismiss flash messages after 5 seconds

## 📝 Best Practices

### For Users:
1. **Keep sidebar collapsed** on smaller screens for more workspace
2. **Use filters** on Deadlines page to focus on what matters
3. **Check Dashboard daily** for upcoming deadlines
4. **Enable reminders** in Settings to never miss deadlines

### For Developers:
1. **Always use components** instead of custom headers/footers
2. **Follow Bootstrap 5 classes** for consistency
3. **Test on mobile** using browser dev tools (F12 > Toggle Device Toolbar)
4. **Use semantic HTML** for better accessibility
5. **Add ARIA labels** for screen readers when needed

## 🐛 Troubleshooting

### Sidebar won't toggle?
- Check if `modern-app.js` is loaded (F12 > Console)
- Verify `sidebarToggle` button has correct ID
- Clear localStorage: `localStorage.clear()`

### Styles not showing?
- Check if `modern-style.css` is loaded (F12 > Network tab)
- Clear browser cache (Ctrl+Shift+R)
- Verify Bootstrap CDN is accessible

### Page looks broken on mobile?
- Ensure viewport meta tag is present in `head.php`
- Check responsive breakpoints in CSS
- Test on actual mobile device, not just browser resize

## 📞 Support

If you encounter issues:
1. Check browser console for errors (F12)
2. Verify XAMPP is running (Apache + MySQL)
3. Check database connection in `config/database.php`
4. Review error logs in `php_error.log`

---

**Version**: 2.0 - Modern UI  
**Last Updated**: October 23, 2025  
**Framework**: Bootstrap 5.3.2 + PHP 7.4+
