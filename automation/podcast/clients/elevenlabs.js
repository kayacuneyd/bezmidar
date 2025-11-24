/**
 * ElevenLabs API Client
 * Text-to-Speech with multilingual Turkish support
 */

import axios from 'axios';
import fs from 'fs';

export default class ElevenLabsClient {
  constructor() {
    this.apiKey = process.env.ELEVENLABS_API_KEY;
    this.voiceId = process.env.ELEVENLABS_VOICE_ID || 'JBFqnCBsd6RMkjVDRZzb'; // Default: George
    this.baseUrl = 'https://api.elevenlabs.io/v1';
  }

  async textToSpeech(text, outputPath) {
    const url = `${this.baseUrl}/text-to-speech/${this.voiceId}`;

    console.log(`🎙️ Seslendirme başlıyor... (${text.length} karakter)`);

    try {
      const response = await axios.post(
        url,
        {
          text: text,
          model_id: 'eleven_multilingual_v2', // Best for Turkish
          voice_settings: {
            stability: 0.6, // Lower = more expressive
            similarity_boost: 0.75, // Higher = closer to voice
            style: 0.2,
            use_speaker_boost: true
          }
        },
        {
          headers: {
            'xi-api-key': this.apiKey,
            'Content-Type': 'application/json'
          },
          responseType: 'arraybuffer'
        }
      );

      fs.writeFileSync(outputPath, Buffer.from(response.data));
      console.log(`✅ Ses dosyası kaydedildi: ${outputPath}`);

      return outputPath;
    } catch (error) {
      if (error.response) {
        const errorText = Buffer.from(error.response.data).toString('utf-8');
        throw new Error(`ElevenLabs API hatası: ${error.response.status} - ${errorText}`);
      }
      throw error;
    }
  }

  /**
   * List available voices (for setup)
   */
  async listVoices() {
    const response = await axios.get(`${this.baseUrl}/voices`, {
      headers: { 'xi-api-key': this.apiKey }
    });

    return response.data.voices.map(v => ({
      id: v.voice_id,
      name: v.name,
      language: v.labels?.language,
      gender: v.labels?.gender,
      accent: v.labels?.accent
    }));
  }

  /**
   * Get voice details
   */
  async getVoiceInfo(voiceId = this.voiceId) {
    const response = await axios.get(`${this.baseUrl}/voices/${voiceId}`, {
      headers: { 'xi-api-key': this.apiKey }
    });

    return response.data;
  }

  /**
   * Check character quota (for monitoring)
   */
  async getQuota() {
    const response = await axios.get(`${this.baseUrl}/user`, {
      headers: { 'xi-api-key': this.apiKey }
    });

    return {
      characterCount: response.data.subscription.character_count,
      characterLimit: response.data.subscription.character_limit,
      remaining: response.data.subscription.character_limit - response.data.subscription.character_count
    };
  }
}
