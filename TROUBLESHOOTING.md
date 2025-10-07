# Troubleshooting Guide - HHC DBMS

Common issues and solutions for deploying and running the HHC Takoradi Church Management System.

## Table of Contents
- [Deployment Issues](#deployment-issues)
- [Database Problems](#database-problems)
- [Authentication Issues](#authentication-issues)
- [Performance Problems](#performance-problems)
- [UI/Display Issues](#ui-display-issues)
- [General Errors](#general-errors)

---

## Deployment Issues

### ❌ GitHub Actions Workflow Fails

**Symptoms:**
- Red X on deployment in Actions tab
- Deployment does not complete

**Common Causes & Solutions:**

#### 1. FTP Connection Failed
```
Error: Cannot connect to FTP server
```

**Solutions:**
- ✓ Verify FTP credentials in GitHub Secrets
- ✓ Check InfinityFree account is active
- ✓ Confirm FTP server address is correct
- ✓ Test FTP connection with FileZilla first
- ✓ Check if IP is blocked (some hosts have restrictions)

**How to verify:**
```bash
# Test FTP connection locally
ftp ftp.yourdomain.com
# Enter username and password when prompted
```

#### 2. Missing or Incorrect Secrets
```
Error: secrets.FTP_SERVER is undefined
```

**Solution:**
1. Go to GitHub repository → Settings
2. Navigate to Secrets and variables → Actions
3. Verify all three secrets exist:
   - `FTP_SERVER`
   - `FTP_USERNAME`
   - `FTP_PASSWORD`
4. Check for typos or extra spaces
5. Re-create secrets if needed

#### 3. Permission Denied
```
Error: 550 Permission denied
```

**Solution:**
- ✓ Check FTP user has write permissions
- ✓ Verify server-dir path is correct (`./htdocs/`)
- ✓ Contact InfinityFree support if persistent

#### 4. Timeout During Upload
```
Error: Connection timeout
```

**Solution:**
- ✓ Large files may cause timeout
- ✓ Check internet connectivity
- ✓ Retry deployment
- ✓ Consider excluding large unnecessary files

---

## Database Problems

### ❌ Database Connection Failed

**Symptoms:**
- "Connection failed" error on pages
- Cannot login or view data
- Health check shows red for database

**Solutions:**

#### 1. Incorrect Credentials
```php
// Check hhc_dbms/db.php
$servername = "sql205.infinityfree.com";  // Verify this
$username = "if0_38624283";                // Verify this
$password = "your_password";               // Check password
$dbname = "if0_38624283_hhctak";          // Verify database name
```

**How to fix:**
1. Log into InfinityFree Control Panel
2. Go to MySQL Databases
3. Verify:
   - Database hostname
   - Database name
   - Database username
   - Reset password if needed
4. Update `db.php` with correct values

#### 2. Database Not Created
```
Error: Unknown database 'if0_38624283_hhctak'
```

**Solution:**
1. Log into InfinityFree Control Panel
2. Create MySQL database
3. Note the database name
4. Update `db.php`

#### 3. Tables Don't Exist
```
Error: Table 'MEMBERS' doesn't exist
```

**Solution:**
1. Navigate to `/update_members_database.php`
2. Run database migration
3. Or use phpMyAdmin to create tables manually
4. Import schema from SQL files if available

#### 4. Connection Limit Reached
```
Error: Too many connections
```

**Solution:**
- ✓ This is a hosting limit on InfinityFree
- ✓ Wait a few minutes and retry
- ✓ Consider upgrading hosting plan
- ✓ Optimize queries to use fewer connections

---

## Authentication Issues

### ❌ Cannot Login

**Symptoms:**
- Login page loads but credentials don't work
- Redirects back to login page
- "Invalid credentials" message

**Solutions:**

#### 1. User Not Registered
**Solution:**
1. Go to `/register.php`
2. Create a new account
3. Try logging in again

#### 2. Session Not Working
**Solution:**
Check session settings in PHP files:
```php
// Verify session_start() is called
session_start();

// Check session timeout
$timeout_duration = 900; // 15 minutes
```

**Fix:**
- ✓ Ensure cookies are enabled in browser
- ✓ Clear browser cache and cookies
- ✓ Check HTTPS is enabled (secure cookies require HTTPS)

#### 3. Password Hash Mismatch
**Solution:**
- ✓ Re-register the user
- ✓ Reset password in database using phpMyAdmin
- ✓ Ensure `password_hash()` is used for new passwords

### ❌ Session Timeout Too Frequent

**Symptoms:**
- Logged out after a few minutes
- Session expires unexpectedly

**Solution:**
Adjust session timeout in PHP files:
```php
// Find this line and increase timeout
$timeout_duration = 900; // Current: 15 minutes

// Change to:
$timeout_duration = 1800; // 30 minutes
// or
$timeout_duration = 3600; // 1 hour
```

---

## Performance Problems

### ❌ Slow Page Loading

**Symptoms:**
- Pages take long to load
- Database queries are slow
- Timeout errors

**Solutions:**

#### 1. Database Query Optimization
```php
// Bad: Not using prepared statements
$sql = "SELECT * FROM MEMBERS WHERE NAME = '$name'";

// Good: Using prepared statements
$stmt = $conn->prepare("SELECT * FROM MEMBERS WHERE NAME = ?");
$stmt->bind_param("s", $name);
```

#### 2. Too Many Records
**Solution:**
- ✓ Implement pagination for large tables
- ✓ Add LIMIT to queries
- ✓ Index frequently queried columns

#### 3. External Resources
**Solution:**
- ✓ Ensure Font Awesome CDN is accessible
- ✓ Check Google Fonts loading
- ✓ Consider local copies of external resources

### ❌ Database Queries Failing

**Symptoms:**
- Error messages with SQL syntax
- Data not saving
- Search not working

**Solution:**
```php
// Check for errors after queries
if (!$result) {
    echo "Error: " . $conn->error;
}

// Use prepared statements
$stmt = $conn->prepare("INSERT INTO MEMBERS (...) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $var1, $var2, $var3);
$stmt->execute();
```

---

## UI/Display Issues

### ❌ Styles Not Loading

**Symptoms:**
- Plain HTML with no styling
- Missing colors and layout
- Broken design

**Solutions:**

#### 1. CSS File Not Found
```html
<!-- Check the path in HTML files -->
<link rel="stylesheet" href="assets/css/main.css">
```

**Fix:**
1. Verify `assets/css/main.css` exists on server
2. Check file was uploaded during deployment
3. Verify path is correct (case-sensitive)

#### 2. MIME Type Issue
**Solution:**
- ✓ Ensure `.css` files are served with correct MIME type
- ✓ Check web server configuration
- ✓ Clear browser cache

#### 3. CSS Syntax Error
**Solution:**
1. Validate CSS in browser DevTools
2. Check for syntax errors in `main.css`
3. Look for unclosed braces or quotes

### ❌ Icons Not Showing

**Symptoms:**
- Boxes/squares instead of icons
- Missing Font Awesome icons

**Solution:**
```html
<!-- Verify Font Awesome is loaded -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
```

**Fix:**
- ✓ Check internet connectivity
- ✓ Verify CDN is accessible
- ✓ Clear browser cache
- ✓ Try different CDN if blocked

### ❌ Images Not Displaying

**Symptoms:**
- Broken image icons
- Logo not showing

**Solution:**
1. Check image file was uploaded: `hhctak.jpg`
2. Verify image path in HTML:
   ```html
   <img src="hhctak.jpg" alt="HHC Takoradi Logo">
   ```
3. Check file permissions (should be 644)
4. Verify file format is supported

---

## General Errors

### ❌ PHP Errors Visible

**Symptoms:**
- PHP warnings and notices on pages
- Error messages visible to users

**Solution for Production:**
```php
// Add to top of PHP files or php.ini
error_reporting(0);
ini_set('display_errors', 0);

// For development, use:
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### ❌ 404 Not Found

**Symptoms:**
- Page not found errors
- Links don't work

**Solutions:**

#### 1. Incorrect Path
```html
<!-- Wrong -->
<a href="/view_members.php">Members</a>

<!-- Correct (relative path) -->
<a href="view_members.php">Members</a>
```

#### 2. File Not Uploaded
**Solution:**
1. Check GitHub Actions deployment log
2. Verify file exists on server via FTP
3. Re-run deployment if needed

### ❌ 500 Internal Server Error

**Symptoms:**
- Server error on page load
- No specific error message

**Solutions:**

#### 1. PHP Syntax Error
**Solution:**
1. Check PHP error logs in InfinityFree
2. Run local PHP syntax check:
   ```bash
   php -l filename.php
   ```
3. Fix syntax errors and redeploy

#### 2. PHP Version Incompatibility
**Solution:**
- ✓ Ensure PHP 8.0+ is used
- ✓ Check InfinityFree PHP version settings
- ✓ Update code for PHP compatibility

#### 3. File Permissions
**Solution:**
- ✓ Set directories to 755
- ✓ Set PHP files to 644
- ✓ Check via FTP client

---

## Health Check Diagnostics

Use `/health.php` to diagnose issues:

### Reading Health Check Results

```
✅ Database: Connection successful
   → Database is working properly

✅ PHP Version: 8.1.x
   → PHP version is compatible

✅ Files: All required files present
   → Deployment completed successfully

✅ Session: Support enabled
   → Authentication will work

✅ MySQLi: Extension loaded
   → Database operations available
```

### Common Health Check Issues

#### Red X on Database
1. Check database credentials in `db.php`
2. Verify database is active
3. Check database user permissions

#### Red X on Files
1. Re-run deployment
2. Check FTP upload completed
3. Verify all files in htdocs/

#### Red X on MySQLi
1. Contact hosting support
2. Verify PHP extensions are enabled
3. Check PHP configuration

---

## Emergency Recovery

### 🚨 Site is Down

**Immediate Steps:**
1. Check health.php for diagnostics
2. Review recent changes in GitHub
3. Check InfinityFree account status
4. Review deployment logs

**Rollback Procedure:**
1. Go to GitHub Actions
2. Find last successful deployment
3. Re-run that workflow
4. Or manually upload previous version via FTP

### 🚨 Data Loss

**Recovery:**
1. Check if database backup exists
2. Restore from InfinityFree backup
3. Re-import data if backup available
4. Contact InfinityFree support

---

## Getting Help

### Before Asking for Help

✓ Run `/health.php` and note results
✓ Check GitHub Actions deployment logs
✓ Review PHP error logs in hosting panel
✓ Try solutions in this guide
✓ Clear browser cache and retry

### Where to Get Help

1. **Repository Issues**
   - GitHub Issues tab
   - Provide: health.php results, error logs

2. **Hosting Issues**
   - InfinityFree Support
   - Control Panel → Support

3. **Development Issues**
   - PHP Documentation
   - Stack Overflow
   - GitHub Discussions

### Information to Provide

When reporting issues:
- [ ] Error message (exact text)
- [ ] Health check results
- [ ] Deployment logs (if applicable)
- [ ] Steps to reproduce
- [ ] Browser and version
- [ ] Screenshots if UI issue

---

## Preventive Measures

### Regular Maintenance

✓ **Weekly**: Check health.php
✓ **Monthly**: Review error logs
✓ **Monthly**: Test all features
✓ **Quarterly**: Update dependencies
✓ **Quarterly**: Security review

### Best Practices

1. **Always test locally** before deploying
2. **Use version control** for all changes
3. **Keep backups** of database
4. **Monitor** health.php regularly
5. **Document** custom changes

### Testing Checklist

Before deploying changes:
- [ ] Run `./test-deployment.sh`
- [ ] Test PHP syntax: `php -l file.php`
- [ ] Test locally if possible
- [ ] Review all changes in git diff
- [ ] Ensure no sensitive data in commits

---

## Additional Resources

- [DEPLOYMENT.md](DEPLOYMENT.md) - Full deployment guide
- [QUICKSTART.md](QUICKSTART.md) - Quick setup guide
- [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) - Verification steps
- [ARCHITECTURE.md](ARCHITECTURE.md) - System architecture

---

**Last Updated**: 2025
**Version**: 1.0

*If your issue is not covered here, please open an issue on GitHub with details.*
