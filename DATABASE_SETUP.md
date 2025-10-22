# Create TaxMindr Database Schema - Step by Step

## Method 1: Using phpMyAdmin (Recommended for Beginners)

### Step 1: Access phpMyAdmin
1. Make sure XAMPP Apache and MySQL are running
2. Open your browser
3. Go to: **http://localhost/phpmyadmin**

### Step 2: Create the Database
1. Click **"New"** in the left sidebar (or click "Databases" tab)
2. In "Create database" section:
   - Database name: `taxmindr`
   - Collation: `utf8mb4_unicode_ci`
3. Click **"Create"** button

![You should see: "Database taxmindr has been created."]

### Step 3: Import the Schema
1. Click on **"taxmindr"** database in the left sidebar (it should now appear in the list)
2. Click the **"Import"** tab at the top
3. Click **"Choose File"** button
4. Navigate to and select:
   ```
   C:\xampp\htdocs\taxmindr\taxmindr\database\schema.sql
   ```
5. Leave all other settings as default
6. Scroll down and click **"Go"** button at the bottom

![You should see: "Import has been successfully finished, 10 queries executed."]

### Step 4: Verify Tables Were Created
1. In the left sidebar, click on the **"taxmindr"** database
2. You should now see 10 tables:
   - ✅ activity_log
   - ✅ atc_codes
   - ✅ filing_submissions
   - ✅ reminder_logs
   - ✅ reminder_settings
   - ✅ tax_deadlines
   - ✅ tax_types
   - ✅ tax_updates
   - ✅ users
   - ✅ withholding_records
   - ✅ withholding_uploads

### Step 5: Verify Structure
1. Click on any table (e.g., **"users"**)
2. Click the **"Structure"** tab
3. You should see all the columns defined for that table

---

## Method 2: Using the Automatic Install Script (Easier!)

### Just visit this URL in your browser:
```
http://localhost/taxmindr/taxmindr/scripts/install.php
```

This will automatically:
1. ✅ Create the database
2. ✅ Create all 10 tables
3. ✅ Insert sample tax types (Income Tax, VAT, Withholding, etc.)
4. ✅ Insert sample ATC codes

**This is the easiest method!**

---

## Method 3: Using MySQL Command Line

### If you prefer command line:

1. Open XAMPP Control Panel
2. Click **"Shell"** button
3. Type these commands:

```bash
# Connect to MySQL
mysql -u root -p
# (Press Enter when asked for password - default is empty)

# Create database
CREATE DATABASE taxmindr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Use the database
USE taxmindr;

# Import schema
SOURCE C:/xampp/htdocs/taxmindr/taxmindr/database/schema.sql;

# Verify tables
SHOW TABLES;

# Exit
EXIT;
```

---

## Verify Installation

After creating the schema, test your setup:

### Visit the connection test page:
```
http://localhost/taxmindr/taxmindr/scripts/test_connection.php
```

This will show you:
- ✅ PHP version
- ✅ Database connection status
- ✅ All tables created
- ✅ Sample data status

---

## Troubleshooting

### Error: "Access denied for user 'root'"
- **Solution:** Check if you have set a MySQL password
- Update `config/database.php` with your password

### Error: "Table already exists"
- **Solution:** Database already exists. You can:
  - Drop the database first (in phpMyAdmin, select database → Operations → Drop)
  - Or skip and use the existing tables

### Error: "Cannot open file"
- **Solution:** Make sure the file path is correct:
  ```
  C:\xampp\htdocs\taxmindr\taxmindr\database\schema.sql
  ```

### Tables not showing up
- **Solution:** 
  - Refresh phpMyAdmin (press F5)
  - Click on the database name in left sidebar
  - Check that MySQL is running in XAMPP

---

## What's in the Schema?

The schema.sql file creates:

### Core Tables:
1. **users** - User accounts and profiles
2. **tax_types** - Philippine tax types (Income Tax, VAT, etc.)
3. **tax_deadlines** - User's tax filing deadlines
4. **reminder_settings** - Email/SMS reminder preferences
5. **reminder_logs** - History of sent reminders

### Tax Management:
6. **tax_updates** - BIR announcements and updates
7. **withholding_uploads** - Uploaded withholding lists
8. **withholding_records** - Individual withholding entries
9. **filing_submissions** - Filed tax returns archive

### Reference Data:
10. **atc_codes** - Alphanumeric Tax Codes
11. **activity_log** - User activity tracking

---

## Next Steps

Once the schema is created:

1. ✅ **Run the install script** to populate sample data:
   ```
   http://localhost/taxmindr/taxmindr/scripts/install.php
   ```

2. ✅ **Register your first account:**
   ```
   http://localhost/taxmindr/taxmindr/public/register.php
   ```

3. ✅ **Login and explore:**
   ```
   http://localhost/taxmindr/taxmindr/public/login.php
   ```

---

**Recommendation:** Use **Method 2** (automatic install script) - it's the easiest and fastest way! 🚀
