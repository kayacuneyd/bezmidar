/**
 * Anthropic Claude API Client
 * Generates podcast scripts in natural Turkish with smart format selection
 */

import Anthropic from '@anthropic-ai/sdk';

export default class AnthropicClient {
  constructor() {
    this.client = new Anthropic({
      apiKey: process.env.ANTHROPIC_API_KEY
    });
  }

  /**
   * Select best format based on topic content
   */
  selectFormat(topicPrompt) {
    const lower = topicPrompt.toLowerCase();

    // Quick-tip for short/fast content
    if (lower.includes('kısa') || lower.includes('hızlı') || lower.includes('ipucu') || lower.includes('özet')) {
      return 'quick-tip';
    }

    // Default: Solo monolog for all other topics
    return 'solo';
  }

  /**
   * Get format-specific system prompt
   */
  getSystemPrompt(format = 'solo') {
    const baseRules = `
## Hedef Kitlesini UNUTMA:
- Almanya'da yaşayan Türk veliler
- 25-55 yaş arası
- Çocuk eğitimi konusunda karar vermek isteyen

## Ses Tonu ve Üslup:
- "Siz" değil "sen" diye hitap et (samimi ama aşırıya kaçma)
- Konuşma dilinde yaz
- Almanca terimleri parantez içinde açıkla: "Gymnasium (lise)"
- Kısa cümleler kullan (15-20 kelime max)
- Doğal bağlaçlar: "Hani derler ya", "Bakın", "Yani", "Mesela"

## Teknik Kurallar:
- Doğal duraklamalar için virgül kullan
- [nefes] veya ... gibi işaretler KULLANMA
- Emoji KULLANMA
- Seslendirmesi zor noktalama işaretlerinden kaçın
`;

    const formats = {
      solo: `Sen 'Dijital Mentor' platformunun kurucusu Cüneyt Kaya'nın Dijital Mentor Ekipler Amirisin. Tek kişilik podcast yapıyorsun.
${baseRules}

## İçerik Yapısı:
1. Giriş: "${process.env.PODCAST_INTRO || 'Merhaba Dijital Mentor ailesi'}"
2. Ana içerik: 3-4 ana başlık
3. Pratik öneriler
4. Kapanış: "${process.env.PODCAST_OUTRO || 'Gelecek bölümde görüşmek üzere hoşçakalın'}"

## Uzunluk: 600-800 kelime (4-5 dakika)

Şimdi verilen konu hakkında podcast metni yaz.`,

      'quick-tip': `Sen hızlı ipuçları veren, özet bilgi paylaşan bir podcast yapıyorsun.
${baseRules}

## Format Kuralları:
- Direkt konuya gir, giriş yapma
- 3-4 madde halinde özet bilgi ver
- Her madde 2-3 cümle olsun
- Kapanışı kısa tut

## Uzunluk: 300-400 kelime (2-3 dakika)

Şimdi verilen konu hakkında hızlı ipuçları ver.`
    };

    return formats[format] || formats.solo;
  }

  async generatePodcastScript(topicPrompt, title = '', description = '') {
    // Auto-select format based on topic
    const format = this.selectFormat(topicPrompt);
    console.log(`🎙️ Format seçildi: ${format.toUpperCase()}`);

    const systemPrompt = this.getSystemPrompt(format);
    const userPrompt = `Konu: ${topicPrompt}${title ? `\nBaşlık: ${title}` : ''}${description ? `\nAçıklama: ${description}` : ''}`;

    const message = await this.client.messages.create({
      model: 'claude-sonnet-4-20250514',
      max_tokens: 2000,
      messages: [
        {
          role: 'user',
          content: `${systemPrompt}\n\n${userPrompt}`
        }
      ]
    });

    const script = message.content[0].text;

    // Validate length
    const wordCount = script.split(/\s+/).length;
    if (wordCount < 400) {
      console.warn(`⚠️ Script kısa (${wordCount} kelime), ideal: 600-800`);
    }

    console.log(`✅ Script oluşturuldu: ${wordCount} kelime, format: ${format}`);

    return script;
  }
}
