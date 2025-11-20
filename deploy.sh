#!/bin/bash

# DijitalMentor Deployment Script
# Bu script kodu GitHub'a push edip hosting'e deploy eder

# Hata durumunda dur
set -e

SSH_KEY="$HOME/.ssh/bezmidar_deploy"
SERVER_USER="u553245641"
SERVER_IP="185.224.137.82"
REMOTE_DIR="~/public_html"

echo "🚀 DijitalMentor Deployment Başlıyor..."

# SSH Key kontrolü
if [ ! -f "$SSH_KEY" ]; then
    echo "❌ Hata: SSH anahtarı bulunamadı: $SSH_KEY"
    echo "Lütfen anahtarın doğru yerde olduğundan emin olun."
    exit 1
fi

# 1. Git commit (opsiyonel mesaj)
if [ -n "$1" ]; then
    COMMIT_MSG="$1"
else
    COMMIT_MSG="Update: $(date '+%Y-%m-%d %H:%M')"
fi

echo "📝 Git commit: $COMMIT_MSG"
# Değişiklik varsa commit yap, yoksa devam et
if [[ `git status --porcelain` ]]; then
  git add .
  git commit -m "$COMMIT_MSG"
  git push origin master
else
  echo "ℹ️  Değişiklik yok, git push atlanıyor."
fi

# 2. Build
echo "🔨 Build alınıyor..."
npm run build

# 3. Hosting'e yükle
echo "📤 Hosting'e yükleniyor..."

# Önce eski build dosyalarını temizle
echo "🧹 Eski dosyalar temizleniyor..."
ssh -p 65002 -i "$SSH_KEY" -o StrictHostKeyChecking=no \
  "$SERVER_USER@$SERVER_IP" \
  "rm -rf $REMOTE_DIR/_app && rm -f $REMOTE_DIR/index.html $REMOTE_DIR/favicon.* $REMOTE_DIR/logo.svg $REMOTE_DIR/manifest.json"

# Yeni dosyaları yükle
echo "📦 Yeni dosyalar yükleniyor..."
rsync -avz \
  -e "ssh -p 65002 -i $SSH_KEY -o StrictHostKeyChecking=no" \
  build/ \
  "$SERVER_USER@$SERVER_IP:$REMOTE_DIR/"

echo "✅ Deployment başarıyla tamamlandı!"
echo "🌐 Site: https://dijitalmentor.de"
