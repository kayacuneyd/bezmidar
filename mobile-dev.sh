#!/bin/bash

# Get local IP
IP=$(ipconfig getifaddr en0)
if [ -z "$IP" ]; then
    IP=$(ipconfig getifaddr en1)
fi

if [ -z "$IP" ]; then
    echo "Error: Could not detect local IP address."
    exit 1
fi

echo "Detected Local IP: $IP"

# Update capacitor.config.ts (simple sed replacement for demo purposes, ideally use a parser)
# This is a bit hacky, but effective for this specific file structure
sed -i '' "s|url: 'http://.*:5173'|url: 'http://$IP:5173'|g" capacitor.config.ts

echo "Updated capacitor.config.ts with IP: $IP"

# Sync Capacitor
echo "Syncing Capacitor..."
npx cap sync

# Run Dev Server
echo "Starting Dev Server..."
echo "You can now open Android Studio (npx cap open android) or Xcode (npx cap open ios)"
npm run dev -- --host
