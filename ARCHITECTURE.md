# Deployment Architecture - HHC DBMS

## System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                     HHC DBMS Architecture                        │
└─────────────────────────────────────────────────────────────────┘

┌──────────────┐         ┌──────────────┐         ┌──────────────┐
│   Developer  │         │    GitHub    │         │ InfinityFree │
│              │         │  Repository  │         │   Hosting    │
│              │────────▶│              │────────▶│              │
│  Local Dev   │  Push   │   Actions    │  Deploy │  Production  │
└──────────────┘         └──────────────┘         └──────────────┘
                                │                         │
                                │                         │
                                ▼                         ▼
                         ┌──────────────┐         ┌──────────────┐
                         │  Workflow    │         │   MySQL DB   │
                         │  CI/CD       │         │              │
                         └──────────────┘         └──────────────┘
```

## Deployment Flow

### 1. Code Development
```
Developer writes code
    │
    ├─▶ Local testing
    │
    ├─▶ Commit changes
    │
    └─▶ Push to GitHub
```

### 2. GitHub Actions Trigger
```
Push to main branch
    │
    ├─▶ Trigger: deploy.yml workflow
    │
    ├─▶ Step 1: Checkout code
    │
    ├─▶ Step 2: Setup PHP 8.1
    │
    └─▶ Step 3: Deploy via FTP
```

### 3. FTP Deployment
```
GitHub Actions Runner
    │
    ├─▶ Connect to FTP server
    │   (using secrets)
    │
    ├─▶ Upload files to htdocs/
    │   (exclude .git, .md files)
    │
    └─▶ Deployment complete ✓
```

### 4. Production Environment
```
InfinityFree Server
    │
    ├─▶ PHP 8.1+ processes requests
    │
    ├─▶ MySQL database stores data
    │
    ├─▶ Web server serves application
    │
    └─▶ Users access via browser
```

## Technology Stack

### Frontend
- **HTML5**: Structure and markup
- **CSS3**: Styling with CSS variables
- **JavaScript**: Interactive features
- **Font Awesome**: Icons
- **Google Fonts**: Typography (Inter)

### Backend
- **PHP 8.1+**: Server-side logic
- **MySQL 5.7+**: Database
- **Sessions**: User authentication
- **MySQLi**: Database connectivity

### Deployment
- **GitHub**: Version control
- **GitHub Actions**: CI/CD automation
- **FTP**: File transfer
- **InfinityFree**: Web hosting

## File Structure in Production

```
htdocs/
├── add_member.php          # Add new members
├── view_members.php        # View/edit members
├── view_tithes.php         # Manage tithes
├── view_departments.php    # Manage departments
├── index.php               # Dashboard (requires auth)
├── login.php               # User login
├── register.php            # User registration
├── logout.php              # User logout
├── health.php              # System health check
├── db.php                  # Database connection
├── process_login.php       # Login handler
├── process_register.php    # Registration handler
├── update_database.php     # CLI database update
├── update_members_database.php  # Web DB update
├── form.html               # Member form
├── thanks.html             # Success page
├── alert.js                # Alert notifications
├── hhctak.jpg              # Church logo
└── assets/
    └── css/
        └── main.css        # Main stylesheet
```

## Database Structure

```
MySQL Database: if0_38624283_hhctak
│
├─▶ MEMBERS
│   ├── ID (Primary Key)
│   ├── NAME
│   ├── SEX
│   ├── MARITAL_STATUS
│   ├── LOCATION
│   ├── CONTACT
│   ├── DEPARTMENT_NAME
│   ├── date_of_birth
│   └── employment_status
│
├─▶ TITHES
│   ├── ENTRY (Primary Key)
│   ├── NAME
│   ├── CONTACT
│   ├── AMOUNT
│   └── DATE
│
├─▶ DEPARTMENTS
│   ├── DEPARTMENT_ID (Primary Key)
│   └── DEPARTMENT_NAME
│
└─▶ USERS
    ├── id (Primary Key)
    ├── username
    ├── email
    └── password (hashed)
```

## Deployment Pipeline

### Automated Deployment (GitHub Actions)

```yaml
Trigger:
  - Push to main branch
  - Manual workflow_dispatch

Steps:
  1. Checkout repository
  2. Setup PHP environment
  3. Connect to FTP server (using secrets)
  4. Upload files to production
  5. Exclude: .git, .md files, .sql scripts

Secrets Required:
  - FTP_SERVER
  - FTP_USERNAME
  - FTP_PASSWORD
```

### Manual Deployment (FTP Client)

```
1. Connect to FTP
   - Server: FTP_SERVER
   - Username: FTP_USERNAME
   - Password: FTP_PASSWORD

2. Navigate to htdocs/

3. Upload files from hhc_dbms/

4. Set permissions:
   - Directories: 755
   - Files: 644

5. Verify deployment
```

## Security Architecture

### Session Management
```
User Login
    │
    ├─▶ Credentials validated
    │
    ├─▶ Session created (15 min timeout)
    │
    ├─▶ Secure cookies set
    │   ├── HttpOnly: true
    │   ├── SameSite: Strict
    │   └── Secure: true
    │
    └─▶ Access granted
```

### Database Security
```
Query Execution
    │
    ├─▶ Prepared statements
    │
    ├─▶ Parameter binding
    │
    ├─▶ Input validation
    │
    └─▶ SQL injection prevention
```

### Authentication Flow
```
┌───────────┐     ┌───────────┐     ┌───────────┐
│  Browser  │────▶│  Session  │────▶│    Page   │
│           │     │  Check    │     │  Content  │
└───────────┘     └───────────┘     └───────────┘
                        │
                        │ Invalid
                        ▼
                  ┌───────────┐
                  │  Redirect │
                  │  to Login │
                  └───────────┘
```

## Monitoring and Health Checks

### Health Check Endpoint (`/health.php`)

```
Checks:
├─▶ Database Connection
├─▶ PHP Version
├─▶ Required Files
├─▶ Session Support
└─▶ MySQLi Extension

Output:
├─▶ ✅ Success (Green)
├─▶ ⚠️ Warning (Yellow)
└─▶ ❌ Error (Red)
```

## Environment Variables

### Production Configuration
```
Database:
  - Host: sql205.infinityfree.com
  - Name: if0_38624283_hhctak
  - User: if0_38624283
  - Pass: [Secure Password]

Session:
  - Timeout: 900 seconds (15 minutes)
  - Secure: true
  - HttpOnly: true
  - SameSite: Strict

PHP:
  - Version: 8.1+
  - Extensions: mysqli, session
```

## Performance Optimization

### Frontend
```
- CSS Variables (efficient theming)
- Minimal JavaScript
- Optimized images
- Google Fonts with display=swap
- Hardware-accelerated animations
```

### Backend
```
- Prepared statements (cached queries)
- Connection pooling
- Efficient SQL queries
- Session management
```

### Hosting
```
- OpCache enabled
- Static asset caching
- Gzip compression
- CDN for external resources
```

## Backup and Recovery

### Backup Strategy
```
Database:
  - Frequency: Daily
  - Method: phpMyAdmin export
  - Storage: InfinityFree backup

Files:
  - Frequency: On deployment
  - Method: Git version control
  - Storage: GitHub repository
```

### Recovery Process
```
1. Identify issue
2. Stop deployment
3. Restore from backup
4. Verify functionality
5. Resume operations
```

## Scaling Considerations

### Current Architecture
- Single server deployment
- Shared hosting (InfinityFree)
- Database on same server

### Future Scaling Options
1. **Horizontal Scaling**
   - Load balancer
   - Multiple web servers
   - Separate database server

2. **Vertical Scaling**
   - Upgrade hosting plan
   - More CPU/RAM
   - Better database performance

3. **Caching Layer**
   - Redis/Memcached
   - Static asset CDN
   - Database query caching

## Maintenance Schedule

```
Daily:
  └─▶ Monitor health.php

Weekly:
  ├─▶ Review deployment logs
  └─▶ Check error logs

Monthly:
  ├─▶ Update dependencies
  ├─▶ Security review
  └─▶ Database optimization

Quarterly:
  ├─▶ Performance audit
  └─▶ Full security audit
```

## Support and Documentation

- 📘 [README.md](README.md) - Main documentation
- 🚀 [QUICKSTART.md](QUICKSTART.md) - 5-minute setup
- 📋 [DEPLOYMENT.md](DEPLOYMENT.md) - Detailed deployment guide
- ✅ [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) - Verification checklist
- 🏥 `/health.php` - System health check

---

**Last Updated**: 2025
**Architecture Version**: 1.0
