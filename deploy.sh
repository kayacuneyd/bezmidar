#!/bin/bash

# Bezmidar Deployment Script
# Bu script kodu GitHub'a push edip hosting'e deploy eder

echo "🚀 Bezmidar Deployment Başlıyor..."

# 1. Git commit (opsiyonel mesaj)
if [ -n "$1" ]; then
    COMMIT_MSG="$1"
else
    COMMIT_MSG="Update: $(date '+%Y-%m-%d %H:%M')"
fi

echo "📝 Git commit: $COMMIT_MSG"
git add .
git commit -m "$COMMIT_MSG"
git push origin master

# 2. Build
echo "🔨 Build alınıyor..."
npm run build

# 3. Hosting'e yükle
echo "📤 Hosting'e yükleniyor..."
rsync -avz --delete \
  -e "ssh -p 65002 -i ~/.ssh/bezmidar_deploy -o StrictHostKeyChecking=no" \
  build/ \
  u553245641@185.224.137.82:~/public_html/

echo "✅ Deployment tamamlandı!"
echo "🌐 Site: https://bezmidar.de"
