# TaxMindr Installation Guide for XAMPP

## Prerequisites
✅ XAMPP installed with Apache and MySQL
✅ PHP already available
✅ phpMyAdmin accessible

## Installation Steps

### 1. Start XAMPP Services
Open XAMPP Control Panel and start:
- ✅ **Apache** (for PHP web server)
- ✅ **MySQL** (for database)

### 2. Access phpMyAdmin
1. Open your browser
2. Go to: `http://localhost/phpmyadmin`
3. You should see the phpMyAdmin dashboard

### 3. Create Database (Option 1: Using phpMyAdmin)
1. In phpMyAdmin, click **"New"** in the left sidebar
2. Database name: `taxmindr`
3. Collation: `utf8mb4_unicode_ci`
4. Click **"Create"**

### 4. Import Database Schema
1. Click on the `taxmindr` database you just created
2. Click the **"Import"** tab at the top
3. Click **"Choose File"**
4. Select: `C:\xampp\htdocs\taxmindr\taxmindr\database\schema.sql`
5. Click **"Go"** at the bottom
6. You should see "Import has been successfully finished"

### 5. Run Installation Script (Option 2: Automatic)
Alternatively, you can run the PHP installation script:
1. Open browser
2. Go to: `http://localhost/taxmindr/taxmindr/scripts/install.php`
3. This will automatically create the database and tables

### 6. Access Your Application
Once the database is set up:

**Homepage:**
```
http://localhost/taxmindr/taxmindr/public/index.php
```

**Register:**
```
http://localhost/taxmindr/taxmindr/public/register.php
```

**Login:**
```
http://localhost/taxmindr/taxmindr/public/login.php
```

## Default Database Configuration

Your application is configured to use XAMPP's default MySQL settings:

```
Host: localhost
Username: root
Password: (empty - no password)
Database: taxmindr
Port: 3306 (default)
```

## Troubleshooting

### Apache Port Conflict
If Apache won't start, port 80 might be in use:
1. In XAMPP Control Panel, click **Config** next to Apache
2. Select **httpd.conf**
3. Change `Listen 80` to `Listen 8080`
4. Access site at: `http://localhost:8080/taxmindr/taxmindr/public/`

### MySQL Port Conflict
If MySQL won't start, port 3306 might be in use:
1. In XAMPP Control Panel, click **Config** next to MySQL
2. Select **my.ini**
3. Change port to 3307
4. Update `config/database.php` accordingly

### "Access Denied" Database Error
If you set a MySQL root password:
1. Open `config/database.php`
2. Update `DB_PASS` with your password

### "Database Does Not Exist" Error
Run the installation script:
```
http://localhost/taxmindr/taxmindr/scripts/install.php
```

## Verify Installation

### Check PHP Info
Create a file `test.php` in `public/` folder:
```php
<?php phpinfo(); ?>
```
Access: `http://localhost/taxmindr/taxmindr/public/test.php`

### Check Database Connection
Access: `http://localhost/taxmindr/taxmindr/scripts/test_connection.php`

## Sample Test Account

After installation, you can create a test account with:
- Email: test@taxmindr.com
- Password: test12345
- User Type: Freelancer

## Next Steps

1. ✅ Register your account
2. ✅ Set up reminder preferences in Settings
3. ✅ Add your first tax deadline
4. ✅ Upload a withholding list to test validation
5. ✅ Explore BIR tax updates

## Need Help?

- Check XAMPP logs in: `C:\xampp\apache\logs\error.log`
- Check PHP errors in browser (displayed when logged in)
- Review MySQL logs in: `C:\xampp\mysql\data\`

---

**Ready to go!** Start XAMPP and access your TaxMindr application! 🚀
