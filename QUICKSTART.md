# Quick Start - Deployment Setup

## 🚀 Deploy Your HHC DBMS in 5 Minutes

### Step 1: Configure GitHub Secrets

1. **Navigate to Repository Settings**
   - Go to https://github.com/bachuwe/hhc_dbms
   - Click **Settings** tab
   - Select **Secrets and variables** → **Actions**

2. **Add Required Secrets**
   
   Click **"New repository secret"** and add each of these:

   | Secret Name | Value | Where to Find |
   |------------|-------|---------------|
   | `FTP_SERVER` | Your FTP host address | InfinityFree Control Panel → FTP Details |
   | `FTP_USERNAME` | Your FTP username | InfinityFree Control Panel → FTP Details |
   | `FTP_PASSWORD` | Your FTP password | InfinityFree Control Panel → FTP Details |

   **Example values:**
   - FTP_SERVER: `ftpupload.net` or similar
   - FTP_USERNAME: `if0_38624283`
   - FTP_PASSWORD: Your InfinityFree FTP password

### Step 2: Find Your InfinityFree FTP Details

1. Log in to [InfinityFree Control Panel](https://infinityfree.com/)
2. Select your hosting account
3. Go to **"Accounts"** → **"FTP Details"**
4. Copy the following:
   - FTP Hostname (this is your FTP_SERVER)
   - FTP Username (this is your FTP_USERNAME)
   - Use your account password (this is your FTP_PASSWORD)

### Step 3: Deploy!

**Option A: Automatic Deployment**
- Simply push code to the `main` branch
- GitHub Actions will automatically deploy

**Option B: Manual Trigger**
1. Go to the **Actions** tab in GitHub
2. Select **"Deploy to InfinityFree"** workflow
3. Click **"Run workflow"**
4. Select `main` branch
5. Click **"Run workflow"** button

### Step 4: Verify Deployment

After deployment completes:

1. **Check Deployment Status**
   - View the workflow run in the Actions tab
   - Green checkmark = successful deployment ✅
   - Red X = deployment failed ❌

2. **Test Your Website**
   - Navigate to: `https://yourdomain.infinityfreeapp.com`
   - Check health: `https://yourdomain.infinityfreeapp.com/health.php`

3. **Initial Setup** (First time only)
   - Run database migration: `/update_members_database.php`
   - Create admin user: `/register.php`
   - Login: `/login.php`

## 🔧 Troubleshooting

### Deployment Failed?

1. **Check GitHub Secrets**
   - Ensure all 3 secrets are set correctly
   - No extra spaces in values
   - Correct FTP hostname format

2. **Check FTP Connection**
   - Test FTP credentials with FileZilla first
   - Ensure FTP account is active on InfinityFree
   - Check if server-dir path is correct (`./htdocs/`)

3. **View Deployment Logs**
   - Go to Actions tab
   - Click on failed workflow run
   - Expand "Deploy via FTP" step
   - Look for error messages

### Common Issues

**"FTP connection failed"**
- ✓ Verify FTP credentials
- ✓ Check InfinityFree account status
- ✓ Try connecting with FTP client manually

**"No such directory"**
- ✓ Ensure `htdocs` folder exists on server
- ✓ Check server-dir path in workflow file

**"Database connection failed"**
- ✓ Update `db.php` with correct credentials
- ✓ Verify database is active in InfinityFree

## 📚 Additional Resources

- [Full Deployment Guide](DEPLOYMENT.md) - Detailed instructions
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [InfinityFree Documentation](https://infinityfree.net/)

## 🆘 Need Help?

1. Check [DEPLOYMENT.md](DEPLOYMENT.md) for detailed troubleshooting
2. Review workflow logs in GitHub Actions
3. Open an issue in the repository

---

**Next Steps After Deployment:**
- [ ] Access your site and verify it loads
- [ ] Run `/health.php` to check system status
- [ ] Execute database migrations if needed
- [ ] Create your first admin user
- [ ] Test all features

Happy Deploying! 🎉
