# Deployment Guide - HHC Takoradi DBMS

## Overview
This document provides instructions for deploying the HHC Takoradi Church Database Management System.

## Hosting Environment
- **Provider**: InfinityFree
- **Database**: MySQL (sql205.infinityfree.com)
- **PHP Version**: 8.1+
- **Database Name**: if0_38624283_hhctak

## Automated Deployment (GitHub Actions)

### Prerequisites
1. GitHub repository with proper access
2. FTP credentials for InfinityFree hosting
3. GitHub Secrets configured

### Setup GitHub Secrets

Navigate to your repository settings and add the following secrets:

```
FTP_SERVER: Your FTP server address (e.g., ftpupload.net)
FTP_USERNAME: Your FTP username
FTP_PASSWORD: Your FTP password
```

**To add secrets:**
1. Go to your GitHub repository
2. Click on "Settings"
3. Navigate to "Secrets and variables" → "Actions"
4. Click "New repository secret"
5. Add each secret with the corresponding name and value

### Deployment Workflow

The deployment workflow is triggered automatically when:
- Code is pushed to the `main` branch
- Manually triggered via GitHub Actions tab

**Deployment Process:**
1. Code is checked out from repository
2. PHP environment is set up
3. Files are deployed via FTP to InfinityFree hosting
4. Excluded files: `.git`, `node_modules`, `.md` documentation, `.sql` scripts

### Manual Trigger

To manually trigger deployment:
1. Go to the "Actions" tab in your GitHub repository
2. Select "Deploy to InfinityFree" workflow
3. Click "Run workflow"
4. Select the branch (usually `main`)
5. Click "Run workflow" button

## Manual Deployment (FTP)

If you need to deploy manually without GitHub Actions:

### Using FileZilla or FTP Client

1. **Connect to FTP Server**
   - Host: Your InfinityFree FTP server
   - Username: Your FTP username
   - Password: Your FTP password
   - Port: 21

2. **Upload Files**
   - Navigate to the `htdocs` folder on the server
   - Upload all files from the `hhc_dbms/` directory
   - Ensure proper file permissions are set

3. **Verify Upload**
   - Check that all PHP files are present
   - Verify the `assets/` directory and its contents
   - Ensure images (hhctak.jpg) are uploaded

## Database Setup

### Initial Database Configuration

The database connection is configured in `db.php`:

```php
$servername = "sql205.infinityfree.com";
$username = "if0_38624283";
$password = "q3alw91v1";
$dbname = "if0_38624283_hhctak";
```

### Database Schema Updates

To update the database schema with new fields:

**Method 1: Web Interface**
1. Navigate to `https://yourdomain.com/update_members_database.php`
2. Click "Update Database"
3. Follow the on-screen instructions

**Method 2: phpMyAdmin**
1. Access phpMyAdmin from InfinityFree control panel
2. Select database: `if0_38624283_hhctak`
3. Run the SQL from `update_members_table.sql`

## Post-Deployment Steps

### 1. Verify Deployment
- [ ] Access the website: `https://yourdomain.com`
- [ ] Test login functionality
- [ ] Verify database connection
- [ ] Check all navigation links

### 2. Database Updates
- [ ] Run database migration if needed (`update_members_database.php`)
- [ ] Verify all tables exist
- [ ] Check data integrity

### 3. Security Checks
- [ ] Ensure `db.php` is not publicly accessible
- [ ] Verify session security settings
- [ ] Test authentication and authorization
- [ ] Check file permissions

### 4. Functionality Testing
- [ ] Test member registration
- [ ] Test member viewing and editing
- [ ] Test tithe management
- [ ] Test department management
- [ ] Verify search functionality

## Environment Configuration

### Production Settings

For production environment, ensure:

1. **PHP Settings**
   - `display_errors = Off` in production
   - `error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT`
   - Session timeout configured (currently 15 minutes)

2. **Security**
   - HTTPS enabled
   - Secure session cookies
   - Database credentials secured
   - File upload restrictions in place

3. **Performance**
   - OpCache enabled
   - Database connection pooling
   - Static asset caching

## Troubleshooting

### Common Issues

**1. Database Connection Failed**
- Verify database credentials in `db.php`
- Check database server status
- Ensure database user has proper permissions

**2. Session Issues**
- Clear browser cookies
- Verify session timeout settings
- Check server session storage

**3. FTP Deployment Failed**
- Verify FTP credentials in GitHub Secrets
- Check FTP server availability
- Ensure sufficient disk space on hosting

**4. File Permissions**
- Set directories to 755
- Set PHP files to 644
- Ensure web server can read files

## Rollback Procedure

If deployment issues occur:

1. **Via GitHub Actions**
   - Go to Actions tab
   - Find last successful deployment
   - Re-run that workflow

2. **Manual Rollback**
   - Keep backup of previous version
   - Upload backup files via FTP
   - Restore database from backup if needed

## Monitoring

### Post-Deployment Monitoring

- Monitor application logs for errors
- Check database performance
- Verify user login activity
- Monitor disk space usage

### Health Check Endpoints

Consider implementing:
- `/health.php` - Basic health check
- Database connectivity check
- Session storage check

## Maintenance

### Regular Maintenance Tasks

1. **Weekly**
   - Review error logs
   - Check disk space
   - Monitor user activity

2. **Monthly**
   - Update dependencies
   - Review security settings
   - Backup database

3. **Quarterly**
   - Performance optimization
   - Security audit
   - Code review

## Support

For deployment issues:
1. Check deployment logs in GitHub Actions
2. Review FTP logs on InfinityFree
3. Check PHP error logs
4. Contact InfinityFree support if needed

## Additional Resources

- [InfinityFree Documentation](https://infinityfree.net/)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [PHP Deployment Best Practices](https://www.php.net/manual/en/install.php)

---

**Last Updated**: 2025
**Version**: 1.0
