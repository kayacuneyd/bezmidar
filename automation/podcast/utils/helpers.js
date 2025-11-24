/**
 * Helper Utilities
 * Webhook, logging, etc.
 */

import axios from 'axios';

/**
 * Update episode via webhook
 */
export async function updateEpisodeViaWebhook(episodeId, data) {
  const webhookUrl = process.env.WEBHOOK_URL;
  const webhookSecret = process.env.WEBHOOK_SECRET;

  if (!webhookUrl) {
    console.warn('⚠️ WEBHOOK_URL not set, skipping database update');
    return;
  }

  const payload = {
    episode_id: episodeId,
    github_run_id: process.env.GITHUB_RUN_ID || null,
    ...data
  };

  try {
    const response = await axios.post(webhookUrl, payload, {
      headers: {
        'Content-Type': 'application/json',
        'X-Webhook-Secret': webhookSecret
      },
      timeout: 10000
    });

    if (response.data.success) {
      console.log(`✅ Episode #${episodeId} güncellendi`);
    } else {
      console.error(`❌ Webhook hatası: ${response.data.error}`);
    }
  } catch (error) {
    console.error('❌ Webhook isteği başarısız:', error.message);
    throw error;
  }
}

/**
 * Log progress with timestamp
 */
export function logProgress(episodeId, status, message) {
  const timestamp = new Date().toISOString();
  console.log(`[${timestamp}] [Episode #${episodeId}] [${status}] ${message}`);
}

/**
 * Generate slug from Turkish text
 */
export function generateSlug(text) {
  let slug = text.toLowerCase();

  // Turkish character normalization
  const charMap = {
    ı: 'i',
    ğ: 'g',
    ü: 'u',
    ş: 's',
    ö: 'o',
    ç: 'c',
    İ: 'i',
    Ğ: 'g',
    Ü: 'u',
    Ş: 's',
    Ö: 'o',
    Ç: 'c'
  };

  slug = slug.replace(/[ıİğĞüÜşŞöÖçÇ]/g, (char) => charMap[char] || char);

  // Remove special characters
  slug = slug.replace(/[^a-z0-9\s-]/g, '');

  // Replace spaces with hyphens
  slug = slug.replace(/\s+/g, '-');

  // Remove duplicate hyphens
  slug = slug.replace(/-+/g, '-');

  // Trim hyphens
  slug = slug.replace(/^-+|-+$/g, '');

  return slug;
}

/**
 * Format duration (seconds to HH:MM:SS or MM:SS)
 */
export function formatDuration(seconds) {
  const hours = Math.floor(seconds / 3600);
  const minutes = Math.floor((seconds % 3600) / 60);
  const secs = seconds % 60;

  if (hours > 0) {
    return `${hours}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
  }
  return `${minutes}:${String(secs).padStart(2, '0')}`;
}

/**
 * Retry function with exponential backoff
 */
export async function retryWithBackoff(fn, maxRetries = 3, baseDelay = 1000) {
  for (let i = 0; i < maxRetries; i++) {
    try {
      return await fn();
    } catch (error) {
      if (i === maxRetries - 1) throw error;

      const delay = baseDelay * Math.pow(2, i);
      console.warn(`⚠️ Attempt ${i + 1} failed, retrying in ${delay}ms...`);
      await new Promise((resolve) => setTimeout(resolve, delay));
    }
  }
}

/**
 * Send notification email (optional)
 */
export async function sendNotificationEmail(episodeId, title, status) {
  // Implementation depends on email service (SendGrid, AWS SES, etc.)
  console.log(`📧 Notification: Episode #${episodeId} "${title}" → ${status}`);
  // TODO: Implement email sending
}

/**
 * Add pronunciation hints for German words in Turkish text
 * Improves TTS (Text-to-Speech) quality by replacing German words with phonetic Turkish equivalents
 */
export function addPronunciationHints(script) {
  const pronunciationMap = {
    // School system
    'Gymnasium': 'Gimnasyum',
    'Realschule': 'Real-şule',
    'Hauptschule': 'Haupt-şule',
    'Grundschule': 'Grunt-şule',
    'Gesamtschule': 'Gezamt-şule',
    'Förderschule': 'Förder-şule',

    // Education processes
    'Ausbildung': 'Aus-bildung',
    'Anmeldung': 'An-meldung',
    'Einschulung': 'Ayn-şulung',
    'Schulanmeldung': 'Şul-anmeldung',

    // Financial terms
    'Kindergeld': 'Kinder-gelt',
    'Elterngeld': 'Eltern-gelt',
    'BAföG': 'Ba-fök',
    'Bürgergeld': 'Bürger-gelt',

    // Common words
    'Abitur': 'Abitur',
    'Kita': 'Kita',
    'Kindergarten': 'Kinder-garten',
    'Schule': 'Şule',
    'Lehrer': 'Lerer',
    'Zeugnis': 'Tsöyg-nis',

    // Levels/Grades
    'Klasse': 'Klase',
    'Stufe': 'Ştufe',
    'Semester': 'Zemester'
  };

  let enhanced = script;

  // Replace each German word with its phonetic equivalent
  for (const [german, phonetic] of Object.entries(pronunciationMap)) {
    // Case-insensitive replacement, preserves context
    const regex = new RegExp(`\\b${german}\\b`, 'gi');
    enhanced = enhanced.replace(regex, phonetic);
  }

  return enhanced;
}
