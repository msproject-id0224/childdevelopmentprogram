#!/bin/bash

# Deploy Script for Child Development Program (CDP) on Hostinger/CloudPanel
# Usage: ./deploy.sh

set -e

echo "🚀 Starting deployment..."

# 1. Pull latest changes (SKIPPED - Git Removed)
# echo "📥 Pulling latest code..."
# git pull origin main

# 2. Install PHP dependencies
echo "📦 Installing PHP dependencies..."
# Ensure we are using the correct PHP version if multiple are installed, or default 'composer'
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Install Node dependencies & Build Assets
echo "🎨 Building frontend assets..."
if command -v npm &> /dev/null; then
    npm ci
    npm run build
else
    echo "⚠️ npm not found. Skipping frontend build. Ensure assets are built or npm is installed."
fi

# 4. Clear & Cache Config/Routes/Views
echo "🧹 Optimizing Laravel..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 5. Run Database Migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# 6. Restart Queue Workers (Supervisor)
# CloudPanel uses 'supervisorctl' usually accessible by root, but if configured with user permissions or via sudo:
if command -v supervisorctl &> /dev/null; then
    echo "🔄 Restarting queue workers..."
    # Attempt restart (might require sudo or root, depending on setup)
    sudo supervisorctl restart all || echo "⚠️ Could not restart supervisor automatically. Run 'sudo supervisorctl restart all' manually if needed."
else
    echo "⚠️ Supervisor not found. Please restart queue workers manually if needed."
fi

# 7. Fix Permissions
# In CloudPanel, the site user usually owns the files. We ensure storage is writable.
echo "🔒 Fixing permissions..."
chmod -R 775 storage bootstrap/cache

# 8. Maintenance Mode (Optional: php artisan up)
if [ -f artisan ]; then
    php artisan up
fi

echo "✅ Deployment complete!"
