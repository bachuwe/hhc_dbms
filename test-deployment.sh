#!/bin/bash

# HHC DBMS Deployment Test Script
# This script helps test the deployment setup locally

set -e  # Exit on error

echo "=================================="
echo "HHC DBMS Deployment Test"
echo "=================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print status
print_status() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✓${NC} $2"
    else
        echo -e "${RED}✗${NC} $2"
    fi
}

# Function to print warning
print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "README.md" ]; then
    echo -e "${RED}Error: Please run this script from the repository root${NC}"
    exit 1
fi

echo "1. Checking repository structure..."
echo ""

# Check for required files
REQUIRED_FILES=(
    ".github/workflows/deploy.yml"
    "DEPLOYMENT.md"
    "QUICKSTART.md"
    "DEPLOYMENT_CHECKLIST.md"
    "README.md"
    ".gitignore"
    ".env.example"
    "hhc_dbms/db.php"
    "hhc_dbms/index.php"
    "hhc_dbms/health.php"
)

all_files_exist=true
for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        print_status 0 "$file exists"
    else
        print_status 1 "$file is missing"
        all_files_exist=false
    fi
done

echo ""
echo "2. Checking PHP files syntax..."
echo ""

# Check PHP syntax for all PHP files
php_errors=false
while IFS= read -r -d '' php_file; do
    if php -l "$php_file" > /dev/null 2>&1; then
        print_status 0 "Syntax OK: $php_file"
    else
        print_status 1 "Syntax error in: $php_file"
        php_errors=true
    fi
done < <(find hhc_dbms -name "*.php" -print0)

echo ""
echo "3. Checking GitHub Actions workflow..."
echo ""

# Check if workflow file is valid YAML
if command -v yamllint &> /dev/null; then
    if yamllint .github/workflows/deploy.yml > /dev/null 2>&1; then
        print_status 0 "Workflow YAML is valid"
    else
        print_status 1 "Workflow YAML has errors"
    fi
else
    print_warning "yamllint not installed, skipping YAML validation"
fi

# Check if workflow references correct secrets
if grep -q "FTP_SERVER" .github/workflows/deploy.yml && \
   grep -q "FTP_USERNAME" .github/workflows/deploy.yml && \
   grep -q "FTP_PASSWORD" .github/workflows/deploy.yml; then
    print_status 0 "All required secrets are referenced"
else
    print_status 1 "Missing required secret references"
fi

echo ""
echo "4. Checking documentation..."
echo ""

# Check if documentation files are not empty
for doc in DEPLOYMENT.md QUICKSTART.md README.md; do
    if [ -s "$doc" ]; then
        lines=$(wc -l < "$doc")
        print_status 0 "$doc ($lines lines)"
    else
        print_status 1 "$doc is empty"
    fi
done

echo ""
echo "5. Checking .gitignore configuration..."
echo ""

# Check if sensitive files are in .gitignore
GITIGNORE_ENTRIES=(
    ".env"
    "*.log"
    ".vscode"
    ".idea"
)

for entry in "${GITIGNORE_ENTRIES[@]}"; do
    if grep -q "$entry" .gitignore; then
        print_status 0 ".gitignore contains: $entry"
    else
        print_warning ".gitignore missing: $entry"
    fi
done

echo ""
echo "6. Security checks..."
echo ""

# Check if db.php contains actual credentials (warning)
if [ -f "hhc_dbms/db.php" ]; then
    if grep -q "your_password_here\|your_username_here" hhc_dbms/db.php; then
        print_warning "Database credentials appear to be placeholders"
    else
        print_status 0 "Database credentials are set (ensure they're correct)"
    fi
fi

# Check if .env file exists (it shouldn't be in repo)
if [ -f ".env" ]; then
    print_warning ".env file exists - ensure it's not committed to git"
else
    print_status 0 "No .env file in repository (good)"
fi

echo ""
echo "7. File permissions check..."
echo ""

# Check if PHP files are readable
php_readable=true
while IFS= read -r -d '' php_file; do
    if [ -r "$php_file" ]; then
        : # File is readable, do nothing
    else
        print_status 1 "Cannot read: $php_file"
        php_readable=false
    fi
done < <(find hhc_dbms -name "*.php" -print0)

if [ "$php_readable" = true ]; then
    print_status 0 "All PHP files are readable"
fi

echo ""
echo "=================================="
echo "Summary"
echo "=================================="
echo ""

if [ "$all_files_exist" = true ] && [ "$php_errors" = false ]; then
    echo -e "${GREEN}✓ All checks passed!${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Set up GitHub Secrets (see QUICKSTART.md)"
    echo "2. Push to main branch or trigger deployment manually"
    echo "3. Monitor deployment in GitHub Actions tab"
    echo "4. Verify deployment at /health.php"
    echo ""
    exit 0
else
    echo -e "${RED}✗ Some checks failed${NC}"
    echo ""
    echo "Please fix the issues above before deploying."
    echo ""
    exit 1
fi
