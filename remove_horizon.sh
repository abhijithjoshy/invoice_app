#!/bin/bash
# Remove Laravel Horizon from the project
echo "Removing Horizon files and configuration..."

# Remove composer dependency
echo "Removing composer dependency..."
composer remove laravel/horizon

# Remove config file
rm -f config/horizon.php

# Remove service provider
rm -f app/Providers/HorizonServiceProvider.php

echo "Remove HorizonServiceProvider from config/app.php manually if present."

echo "Horizon removal complete."
