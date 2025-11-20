export const mockData = {
  teachers: [
    {
      id: 101,
      full_name: 'Ahmet Yılmaz',
      avatar_url: 'https://randomuser.me/api/portraits/men/32.jpg',
      approval_status: 'approved',
      university: 'TU Berlin',
      department: 'Bilgisayar Mühendisliği',
      city: 'Berlin',
      lat: 52.5200,
      lng: 13.4050,
      hourly_rate: 25.00,
      rating_avg: 4.8,
      review_count: 12,
      subjects: [
        { name: 'Matematik', icon: '📐', proficiency_level: 'Uzman' },
        { name: 'Fizik', icon: '⚛️', proficiency_level: 'İleri' }
      ],
      bio: 'Merhaba! Ben Ahmet. Berlin Teknik Üniversitesi\'nde son sınıf öğrencisiyim. Matematik ve Fizik derslerinde yardımcı olabilirim.',
      graduation_year: 2024,
      zip_code: '10115',
      experience_years: 3,
      video_intro_url: 'https://www.youtube.com/embed/dQw4w9WgXcQ',
      reviews: [
        { rating: 5, comment: 'Ahmet hoca çok ilgili.', parent_name: 'Fatma Öztürk', created_at: '2023-11-15' },
        { rating: 4, comment: 'Ders anlatımı güzel.', parent_name: 'Mustafa Arslan', created_at: '2023-11-10' }
      ]
    },
    {
      id: 102,
      full_name: 'Ayşe Demir',
      avatar_url: 'https://randomuser.me/api/portraits/women/44.jpg',
      approval_status: 'approved',
      university: 'LMU München',
      department: 'Alman Dili ve Edebiyatı',
      city: 'München',
      lat: 48.1351,
      lng: 11.5820,
      hourly_rate: 20.00,
      rating_avg: 5.0,
      review_count: 5,
      subjects: [
        { name: 'Almanca', icon: '🇩🇪', proficiency_level: 'Uzman' },
        { name: 'İngilizce', icon: '🇬🇧', proficiency_level: 'İleri' }
      ],
      bio: 'Almanca öğrenmek isteyenlere yardımcı oluyorum. Hem gramer hem de konuşma pratiği yapabiliriz.',
      graduation_year: 2023,
      zip_code: '80331',
      experience_years: 2,
      video_intro_url: 'https://www.youtube.com/embed/dQw4w9WgXcQ',
      reviews: [
        { rating: 5, comment: 'Kızım Ayşe ablasını çok seviyor.', parent_name: 'Fatma Öztürk', created_at: '2023-11-01' }
      ]
    },
    {
      id: 103,
      full_name: 'Mehmet Kaya',
      avatar_url: 'https://randomuser.me/api/portraits/men/85.jpg',
      approval_status: 'pending',
      university: 'RWTH Aachen',
      department: 'Makine Mühendisliği',
      city: 'Aachen',
      lat: 50.7753,
      lng: 6.0839,
      hourly_rate: 18.00,
      rating_avg: 4.5,
      review_count: 3,
      subjects: [
        { name: 'Kimya', icon: '🧪', proficiency_level: 'Orta' },
        { name: 'Biyoloji', icon: '🧬', proficiency_level: 'Orta' },
        { name: 'Matematik', icon: '📐', proficiency_level: 'İleri' }
      ],
      bio: 'Sayısal derslerde zorlanan öğrencilere pratik yöntemlerle ders anlatıyorum.',
      graduation_year: 2025,
      zip_code: '52062',
      experience_years: 1,
      reviews: []
    },
    {
      id: 104,
      full_name: 'Zeynep Çelik',
      avatar_url: 'https://randomuser.me/api/portraits/women/68.jpg',
      approval_status: 'approved',
      university: 'Universität Hamburg',
      department: 'Psikoloji',
      city: 'Hamburg',
      lat: 53.5511,
      lng: 9.9937,
      hourly_rate: 22.00,
      rating_avg: 4.9,
      review_count: 8,
      subjects: [
        { name: 'İngilizce', icon: '🇬🇧', proficiency_level: 'Uzman' },
        { name: 'Türkçe', icon: '🇹🇷', proficiency_level: 'Ana Dil' }
      ],
      bio: 'Öğrencilerin sadece derslerine değil, motivasyonlarına da katkı sağlamayı hedefliyorum.',
      graduation_year: 2024,
      zip_code: '20095',
      experience_years: 4,
      reviews: [
        { rating: 5, comment: 'Zeynep hanım çok kibar.', parent_name: 'Mustafa Arslan', created_at: '2023-11-18' }
      ]
    },
    {
      id: 105,
      full_name: 'Can Yıldız',
      avatar_url: 'https://randomuser.me/api/portraits/men/12.jpg',
      approval_status: 'pending',
      university: 'Goethe Universität Frankfurt',
      department: 'Hukuk',
      city: 'Frankfurt',
      lat: 50.1109,
      lng: 8.6821,
      hourly_rate: 15.00,
      rating_avg: 0.0,
      review_count: 0,
      subjects: [
        { name: 'Tarih', icon: '📜', proficiency_level: 'Orta' },
        { name: 'Coğrafya', icon: '🌍', proficiency_level: 'Orta' }
      ],
      bio: 'Tarih ve Coğrafya derslerinde yardımcı olabilirim.',
      graduation_year: 2026,
      zip_code: '60311',
      experience_years: 0,
      reviews: []
    }
  ],
  subjects: [
    { id: 1, name: 'Matematik', slug: 'matematik', icon: '📐' },
    { id: 2, name: 'Almanca', slug: 'almanca', icon: '🇩🇪' },
    { id: 3, name: 'İngilizce', slug: 'ingilizce', icon: '🇬🇧' },
    { id: 4, name: 'Türkçe', slug: 'turkce', icon: '🇹🇷' },
    { id: 5, name: 'Fizik', slug: 'fizik', icon: '⚛️' },
    { id: 6, name: 'Kimya', slug: 'kimya', icon: '🧪' },
    { id: 7, name: 'Biyoloji', slug: 'biyoloji', icon: '🧬' },
    { id: 8, name: 'Tarih', slug: 'tarih', icon: '📜' },
    { id: 9, name: 'Coğrafya', slug: 'cografya', icon: '🌍' },
    { id: 10, name: 'Müzik', slug: 'muzik', icon: '🎵' },
    { id: 11, name: 'Resim', slug: 'resim', icon: '🎨' },
    { id: 12, name: 'Bilgisayar', slug: 'bilgisayar', icon: '💻' }
  ],
  user: {
    id: 201,
    full_name: 'Demo Veli',
    role: 'parent',
    phone: '+49123456789',
    token: 'mock-token-123',
    approval_status: 'approved',
    is_premium: false,
    premium_expires_at: null
  },
  lessonRequests: [
    {
      id: 1,
      parent_id: 201,
      parent_name: 'Demo Veli',
      subject_id: 1,
      subject_name: 'Matematik',
      title: 'Lise 2 Matematik Desteği',
      description: 'Oğlum için haftada 2 saat matematik dersi arıyorum. Sınavlara hazırlık konusunda tecrübeli olması tercihimizdir.',
      city: 'Berlin',
      budget_range: '20-30€',
      status: 'active',
      created_at: '2023-11-20'
    },
    {
      id: 2,
      parent_id: 202,
      parent_name: 'Mustafa Arslan',
      subject_id: 3,
      subject_name: 'İngilizce',
      title: 'İlkokul İngilizce Konuşma Pratiği',
      description: 'Kızım için oyunlarla İngilizce öğretebilecek bir abla arıyoruz.',
      city: 'München',
      budget_range: '15-25€',
      status: 'active',
      created_at: '2023-11-19'
    }
  ],
  posts: [
    {
      id: 1,
      title: "Çocuğunuz İçin Doğru Öğretmeni Nasıl Seçersiniz?",
      slug: "dogru-ogretmen-secimi",
      excerpt: "Özel ders alırken dikkat etmeniz gereken en önemli kriterleri sizin için derledik.",
      content: `
        <p>Çocuğunuzun eğitim hayatında ona destek olacak bir öğretmen seçmek kritik bir karardır. İşte dikkat etmeniz gerekenler:</p>
        <h3>1. İletişim Becerisi</h3>
        <p>Öğretmenin bilgisi kadar, bu bilgiyi çocuğunuza nasıl aktardığı da önemlidir. İlk görüşmede öğretmenin iletişim tarzına dikkat edin.</p>
        <h3>2. Deneyim</h3>
        <p>Daha önce benzer yaş grubundaki çocuklarla çalışmış mı? Referansları var mı?</p>
        <h3>3. Uyum</h3>
        <p>Çocuğunuzun öğretmeni sevmesi, dersin verimliliğini doğrudan etkiler.</p>
      `,
      author: "Zeynep Yılmaz",
      date: "2023-11-15",
      image: "https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&q=80",
      likes: 45,
      comments: [
        { id: 1, user: "Ahmet K.", text: "Çok faydalı bir yazı olmuş, teşekkürler.", date: "2023-11-16" }
      ]
    },
    {
      id: 2,
      title: "Almanya'da Türkçe Eğitimi Neden Önemli?",
      slug: "almanyada-turkce-egitimi",
      excerpt: "Anadilini iyi bilen çocukların ikinci dili öğrenmesi daha kolaydır. Bilimsel araştırmalar ne diyor?",
      content: `
        <p>Birçok aile, çocuklarının Almanca öğrenmesi için Türkçe konuşmayı azaltması gerektiğini düşünür. Oysa bilimsel araştırmalar tam tersini söylüyor.</p>
        <p>Anadilini iyi bilen çocuklar, soyut düşünme becerilerini daha erken geliştirir ve ikinci bir dili (Almanca) çok daha hızlı öğrenir.</p>
      `,
      author: "Mehmet Demir",
      date: "2023-11-10",
      image: "https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&q=80",
      likes: 120,
      comments: []
    },
    {
      id: 3,
      title: "Online Ders mi, Yüz Yüze Ders mi?",
      slug: "online-vs-yuzyuze-ders",
      excerpt: "Pandemi sonrası artan online dersler verimli mi? Hangi durumlarda hangisi tercih edilmeli?",
      content: `
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
      `,
      author: "Ayşe Kaya",
      date: "2023-11-05",
      image: "https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&q=80",
      likes: 32,
      comments: []
    }
  ],
  conversations: [
    {
      id: 1,
      other_user: { id: 101, full_name: 'Ahmet Yılmaz', avatar_url: 'https://randomuser.me/api/portraits/men/32.jpg' },
      last_message: 'Ders saati konusunda anlaştık mı?',
      last_message_date: '2023-11-20T14:30:00',
      unread_count: 1
    },
    {
      id: 2,
      other_user: { id: 104, full_name: 'Zeynep Çelik', avatar_url: 'https://randomuser.me/api/portraits/women/68.jpg' },
      last_message: 'Merhaba, İngilizce dersi için müsaitliğiniz var mı?',
      last_message_date: '2023-11-19T10:15:00',
      unread_count: 0
    }
  ],
  messages: [
    { id: 1, conversation_id: 1, sender_id: 101, text: 'Merhaba, nasılsınız?', created_at: '2023-11-20T14:00:00' },
    { id: 2, conversation_id: 1, sender_id: 201, text: 'İyiyim teşekkürler, siz?', created_at: '2023-11-20T14:05:00' },
    { id: 3, conversation_id: 1, sender_id: 101, text: 'Ders saati konusunda anlaştık mı?', created_at: '2023-11-20T14:30:00' },
    { id: 4, conversation_id: 2, sender_id: 201, text: 'Merhaba, İngilizce dersi için müsaitliğiniz var mı?', created_at: '2023-11-19T10:15:00' }
  ]
};
