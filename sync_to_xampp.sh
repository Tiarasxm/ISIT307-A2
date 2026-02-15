#!/bin/bash
# Sync ISIT307-A2 to XAMPP htdocs

SOURCE="/Users/imchaitragool/Documents/GitHub/ISIT307-A2"
DEST="/Applications/XAMPP/xamppfiles/htdocs/ISIT307-A2"

echo "Syncing files to XAMPP..."
rsync -av --delete --exclude='.git' --exclude='sync_to_xampp.sh' --exclude='.DS_Store' "$SOURCE/" "$DEST/"

if [ $? -eq 0 ]; then
    echo "✓ Sync completed successfully!"
    echo "Your changes are now live at: http://localhost/ISIT307-A2/"
else
    echo "✗ Sync failed!"
    exit 1
fi
