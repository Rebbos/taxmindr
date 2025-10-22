# TaxMindr Quick Start Guide

## 🚀 Quick Start (3 Steps)

### Step 1: Start XAMPP
1. Open **XAMPP Control Panel**
2. Click **Start** for:
   - ✅ Apache
   - ✅ MySQL

### Step 2: Setup Database
Open your browser and go to:
```
http://localhost/taxmindr/taxmindr/scripts/install.php
```
This will automatically create the database and tables.

### Step 3: Access TaxMindr
```
http://localhost/taxmindr/taxmindr/public/index.php
```

---

## 📍 Important URLs

| Page | URL |
|------|-----|
| **Homepage** | http://localhost/taxmindr/taxmindr/public/index.php |
| **Register** | http://localhost/taxmindr/taxmindr/public/register.php |
| **Login** | http://localhost/taxmindr/taxmindr/public/login.php |
| **Dashboard** | http://localhost/taxmindr/taxmindr/public/dashboard.php |
| **phpMyAdmin** | http://localhost/phpmyadmin |
| **Test Connection** | http://localhost/taxmindr/taxmindr/scripts/test_connection.php |

---

## 🗂️ Project Structure

```
taxmindr/
├── public/              # Web-accessible pages
│   ├── index.php       # Homepage
│   ├── register.php    # User registration
│   ├── login.php       # User login
│   ├── dashboard.php   # Main dashboard
│   ├── deadlines.php   # Tax deadlines
│   ├── upload_withholding.php
│   ├── updates.php     # BIR updates
│   ├── submissions.php # Filing archive
│   └── settings.php    # User settings
│
├── config/             # Configuration files
│   ├── config.php     # App settings
│   └── database.php   # Database connection
│
├── includes/          # Reusable PHP components
│   ├── functions.php  # Helper functions
│   ├── header.php     # Navigation header
│   └── sidebar.php    # Sidebar menu
│
├── assets/           # Frontend resources
│   ├── css/         # Stylesheets
│   ├── js/          # JavaScript
│   └── images/      # Images
│
├── database/        # Database files
│   └── schema.sql  # Database schema
│
├── scripts/        # Utility scripts
│   ├── install.php           # Database installer
│   └── test_connection.php   # Connection tester
│
└── uploads/        # User uploaded files
```

---

## ⚙️ Default Configuration

**Database:**
- Host: `localhost`
- Database Name: `taxmindr`
- Username: `root`
- Password: (empty)
- Port: `3306`

**Application:**
- Timezone: `Asia/Manila`
- Max Upload Size: `10MB`
- Session Timeout: `1 hour`

---

## 🔧 Troubleshooting

### Apache won't start
- **Reason:** Port 80 is already in use
- **Fix:** Change Apache port to 8080 in XAMPP config
- **Then access:** http://localhost:8080/taxmindr/taxmindr/public/

### MySQL won't start
- **Reason:** Port 3306 is already in use
- **Fix:** Stop other MySQL services or change port

### "Database does not exist" error
- **Fix:** Run the installation script:
  ```
  http://localhost/taxmindr/taxmindr/scripts/install.php
  ```

### "Access denied for user 'root'" error
- **Fix:** Check if you have set a MySQL password
- Update `config/database.php` with your password

---

## ✅ Testing Your Installation

### Test Database Connection
```
http://localhost/taxmindr/taxmindr/scripts/test_connection.php
```

### Check PHP Info
Create `test.php` in `public/` folder:
```php
<?php phpinfo(); ?>
```
Access: http://localhost/taxmindr/taxmindr/public/test.php

---

## 📝 First Time Setup

1. **Register an account** at `/register.php`
2. **Login** at `/login.php`
3. **Configure reminders** in Settings
4. **Add your first deadline** in Tax Deadlines
5. **Try uploading** a withholding list

---

## 🎯 Core Features

### 1. Deadline Calendar & Reminders
- Auto-generated tax deadlines
- Email and SMS notifications
- Mark as Filed/Paid
- Activity log tracking

### 2. Tax Updates with Actions
- BIR announcements
- Action items
- Official source links
- Filtered by user type

### 3. Withholding List Validation
- Upload Excel/CSV files
- Validate TINs, ATC codes
- Error detection
- Clean downloads

### 4. Final Check Before Submit
- Critical error flagging
- Receipt/screenshot upload
- Submission confirmation

### 5. Reports & Archive
- Exportable tax reports
- Searchable filing history
- Secure document storage

---

## 🆘 Need Help?

- Check `INSTALLATION.md` for detailed setup
- View Apache logs: `C:\xampp\apache\logs\error.log`
- View MySQL logs: `C:\xampp\mysql\data\`
- Access phpMyAdmin to manage database directly

---

**TaxMindr by ASTIGORITHM** - Making Philippine tax compliance simple! 🇵🇭
