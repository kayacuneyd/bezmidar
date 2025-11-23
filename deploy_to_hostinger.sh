#!/bin/bash
# -----------------------------------------------------------
# Deploy Script for dijitalmentor.de (Hostinger Business)
# By: Cüneyt Kaya
# -----------------------------------------------------------

# Kullanım: ./deploy_to_hostinger.sh "Commit açıklaması"

# 1️⃣ Git commit işlemleri
if [ -z "$1" ]; then
  echo "Lütfen bir commit mesajı girin. Örnek: ./deploy_to_hostinger.sh 'API güncellemesi'"
  exit 1
fi

echo "🔄 Git commit ve push işlemi yapılıyor..."
git add .
git commit -m "$1"
git push origin master

# 2️⃣ Rsync ile dosyaları Hostinger sunucusuna gönder
echo "🚀 Dosyalar Hostinger'a yükleniyor..."

rsync -avz -e "ssh -p 65002" ./server/api/ \
u553245641@185.224.137.82:/home/u553245641/domains/dijitalmentor.de/public_html/api_root/server/api/

# 3️⃣ Sonuç bildirimi
if [ $? -eq 0 ]; then
  echo "✅ Deploy başarıyla tamamlandı!"
else
  echo "❌ Deploy sırasında bir hata oluştu."
fi