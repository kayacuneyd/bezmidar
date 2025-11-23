<?php

// Basit statik blog verisi.
// İleride veritabanına taşımak istersen, bu dosya yerine DB sorgusu kullanabilirsin.

$BLOG_POSTS = [
    [
        'id' => 1,
        'title' => "Çocuğunuz İçin Doğru Öğretmeni Nasıl Seçersiniz?",
        'slug' => "dogru-ogretmen-secimi",
        'excerpt' => "Özel ders alırken dikkat etmeniz gereken en önemli kriterleri sizin için derledik.",
        'content' => '
        <p>Çocuğunuzun eğitim hayatında ona destek olacak bir öğretmen seçmek kritik bir karardır. İşte dikkat etmeniz gerekenler:</p>
        <h3>1. İletişim Becerisi</h3>
        <p>Öğretmenin bilgisi kadar, bu bilgiyi çocuğunuza nasıl aktardığı da önemlidir. İlk görüşmede öğretmenin iletişim tarzına dikkat edin.</p>
        <h3>2. Deneyim</h3>
        <p>Daha önce benzer yaş grubundaki çocuklarla çalışmış mı? Referansları var mı?</p>
        <h3>3. Uyum</h3>
        <p>Çocuğunuzun öğretmeni sevmesi, dersin verimliliğini doğrudan etkiler.</p>
        ',
        'author' => "Zeynep Yılmaz",
        'date' => "2023-11-15",
        'image' => "https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&q=80",
        'likes' => 45,
        'comments' => [
            [
                'id' => 1,
                'user' => "Ahmet K.",
                'text' => "Çok faydalı bir yazı olmuş, teşekkürler.",
                'date' => "2023-11-16"
            ]
        ]
    ],
    [
        'id' => 2,
        'title' => "Almanya'da Türkçe Eğitimi Neden Önemli?",
        'slug' => "almanyada-turkce-egitimi",
        'excerpt' => "Anadilini iyi bilen çocukların ikinci dili öğrenmesi daha kolaydır. Bilimsel araştırmalar ne diyor?",
        'content' => '
        <p>Birçok aile, çocuklarının Almanca öğrenmesi için Türkçe konuşmayı azaltması gerektiğini düşünür. Oysa bilimsel araştırmalar tam tersini söylüyor.</p>
        <p>Anadilini iyi bilen çocuklar, soyut düşünme becerilerini daha erken geliştirir ve ikinci bir dili (Almanca) çok daha hızlı öğrenir.</p>
        ',
        'author' => "Mehmet Demir",
        'date' => "2023-11-10",
        'image' => "https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&q=80",
        'likes' => 120,
        'comments' => []
    ],
    [
        'id' => 3,
        'title' => "Online Ders mi, Yüz Yüze Ders mi?",
        'slug' => "online-vs-yuzyuze-ders",
        'excerpt' => "Pandemi sonrası artan online dersler verimli mi? Hangi durumlarda hangisi tercih edilmeli?",
        'content' => '
        <p>Teknolojinin gelişmesiyle online dersler hayatımızın bir parçası oldu. Ancak her öğrenci için uygun olmayabilir.</p>
        <h3>Online Dersin Avantajları</h3>
        <ul>
          <li>Zaman tasarrufu</li>
          <li>Daha geniş öğretmen havuzu</li>
        </ul>
        <h3>Yüz Yüze Dersin Avantajları</h3>
        <ul>
          <li>Daha iyi odaklanma</li>
          <li>Doğrudan etkileşim</li>
        </ul>
        ',
        'author' => "Ayşe Kaya",
        'date' => "2023-11-05",
        'image' => "https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&q=80",
        'likes' => 32,
        'comments' => []
    ],
];

