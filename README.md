# HHC Takoradi Church Database Management System

![HHC Takoradi](hhc_dbms/hhctak.jpg)

A comprehensive church management system for Holy Hill Chapel Takoradi, built with PHP and MySQL.

## Features

### Member Management
- ✅ Add new members with detailed information
- ✅ View and edit member records
- ✅ Search and filter members
- ✅ Track date of birth and employment status
- ✅ Department assignments

### Tithe Management
- ✅ Record tithe contributions
- ✅ Track tithe history by member
- ✅ Edit and manage tithe records
- ✅ Member dropdown for easy selection

### Department Management
- ✅ Create and manage church departments
- ✅ Assign members to departments
- ✅ View department members

### User Authentication
- ✅ Secure login system
- ✅ Session management with timeout (15 minutes)
- ✅ User registration
- ✅ Secure cookie configuration

### Modern UI/UX
- ✅ Responsive design for all devices
- ✅ Modern Material Design-inspired interface
- ✅ Smooth animations and transitions
- ✅ Font Awesome icons
- ✅ Professional color scheme

## Technology Stack

- **Backend**: PHP 8.1+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Styling**: Custom CSS with CSS Variables
- **Icons**: Font Awesome 6.5.1
- **Fonts**: Google Fonts (Inter)

## Installation

### Prerequisites
- PHP 8.1 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- FTP access (for deployment)

### Local Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/bachuwe/hhc_dbms.git
   cd hhc_dbms
   ```

2. **Configure Database**
   - Edit `hhc_dbms/db.php` with your database credentials
   - Or copy `.env.example` to `.env` and configure

3. **Setup Database Tables**
   - Import the database schema
   - Run `update_database.php` to add new columns:
     ```bash
     php hhc_dbms/update_database.php
     ```
   - Or navigate to `update_members_database.php` in your browser

4. **Configure Web Server**
   - Point document root to `hhc_dbms/` directory
   - Ensure PHP is properly configured
   - Enable required PHP extensions (mysqli, session)

5. **Access the Application**
   - Navigate to `http://localhost/login.php`
   - Create an account or use existing credentials

## Deployment

### Automated Deployment (GitHub Actions)

This project includes automated deployment via GitHub Actions.

1. **Set up GitHub Secrets** (see [DEPLOYMENT.md](DEPLOYMENT.md))
   - `FTP_SERVER`: Your FTP server address
   - `FTP_USERNAME`: Your FTP username
   - `FTP_PASSWORD`: Your FTP password

2. **Deploy**
   - Push to `main` branch to trigger automatic deployment
   - Or manually trigger deployment from Actions tab

For detailed deployment instructions, see [DEPLOYMENT.md](DEPLOYMENT.md).

### Manual Deployment

1. Upload files via FTP to your hosting provider
2. Configure database connection in `db.php`
3. Set proper file permissions (755 for directories, 644 for files)
4. Run database migrations if needed

## Project Structure

```
hhc_dbms/
├── .github/
│   └── workflows/
│       └── deploy.yml          # GitHub Actions deployment workflow
├── hhc_dbms/
│   ├── assets/
│   │   └── css/
│   │       └── main.css        # Main stylesheet
│   ├── add_member.php          # Add new member
│   ├── view_members.php        # View/edit members
│   ├── view_tithes.php         # Tithe management
│   ├── view_departments.php    # Department management
│   ├── index.php               # Dashboard
│   ├── login.php               # Login page
│   ├── register.php            # Registration page
│   ├── db.php                  # Database connection
│   └── ...
├── DEPLOYMENT.md               # Deployment guide
├── .env.example                # Environment configuration template
└── README.md                   # This file
```

## Database Schema

### MEMBERS Table
- `ID` (INT, Primary Key, Auto Increment)
- `NAME` (VARCHAR)
- `SEX` (ENUM: 'Male', 'Female')
- `MARITAL_STATUS` (ENUM: 'Single', 'Married', 'Divorced', 'Widowed')
- `LOCATION` (VARCHAR)
- `CONTACT` (VARCHAR)
- `DEPARTMENT_NAME` (VARCHAR)
- `date_of_birth` (DATE, Nullable)
- `employment_status` (ENUM: 'APPRENTICESHIP', 'EMPLOYED', 'UNEMPLOYED', Nullable)

### TITHES Table
- `ENTRY` (INT, Primary Key, Auto Increment)
- `NAME` (VARCHAR)
- `CONTACT` (VARCHAR)
- `AMOUNT` (DECIMAL)
- `DATE` (DATE)

### DEPARTMENTS Table
- `DEPARTMENT_ID` (INT, Primary Key, Auto Increment)
- `DEPARTMENT_NAME` (VARCHAR, Unique)

### USERS Table
- `id` (INT, Primary Key, Auto Increment)
- `username` (VARCHAR)
- `email` (VARCHAR)
- `password` (VARCHAR, Hashed)

## Security Features

- ✅ Password hashing with PHP's `password_hash()`
- ✅ Prepared statements for SQL queries
- ✅ Session timeout (15 minutes of inactivity)
- ✅ Secure session cookies (HTTPOnly, SameSite, Secure)
- ✅ CSRF protection considerations
- ✅ Input validation and sanitization

## Recent Updates

### Members Table Enhancement
- Added `date_of_birth` field (DATE)
- Added `employment_status` field (ENUM)
- Updated forms to include new fields
- Enhanced search with employment status filter

See [hhc_dbms/README_MEMBERS_UPDATE.md](hhc_dbms/README_MEMBERS_UPDATE.md) for details.

### UI/UX Improvements
- Complete styling overhaul
- Modern design system with CSS variables
- Responsive design for all screen sizes
- Enhanced user interactions and animations

See [hhc_dbms/STYLING_IMPROVEMENTS.md](hhc_dbms/STYLING_IMPROVEMENTS.md) for details.

## Browser Support

- ✅ Chrome (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Edge (latest)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Author

**Ayikem Cosmas Awupuri**

## Acknowledgments

- Holy Hill Chapel Takoradi community
- Font Awesome for icons
- Google Fonts for typography

## Support

For issues, questions, or contributions, please open an issue in the GitHub repository.

---

**Made with ❤️ for HHC Takoradi**
