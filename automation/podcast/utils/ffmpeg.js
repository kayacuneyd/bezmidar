/**
 * FFmpeg Utilities
 * Audio mixing, duration extraction, etc.
 */

import ffmpeg from 'fluent-ffmpeg';
import { promisify } from 'util';
import { exec } from 'child_process';
import axios from 'axios';
import fs from 'fs';
import path from 'path';

const execAsync = promisify(exec);

/**
 * Mix voice audio with background music
 */
export async function mixAudio(voicePath, musicPath, outputPath) {
  return new Promise((resolve, reject) => {
    const musicVolume = process.env.BACKGROUND_MUSIC_VOLUME || '0.1';

    // If no music provided, just re-encode the voice to the target output
    if (!musicPath || !fs.existsSync(musicPath)) {
      console.log('⚠️ No background music provided, exporting voice only');
      ffmpeg()
        .input(voicePath)
        .audioCodec('libmp3lame')
        .audioBitrate('192k')
        .on('end', () => {
          console.log(`✅ Voice-only audio saved: ${outputPath}`);
          resolve(outputPath);
        })
        .on('error', (err) => {
          console.error('FFmpeg voice-only error:', err);
          reject(err);
        })
        .save(outputPath);
      return;
    }

    console.log(`🎚️ Mixing: Voice + Music (volume: ${musicVolume})`);

    ffmpeg()
      .input(voicePath)
      .input(musicPath)
      .complexFilter([
        `[1:a]volume=${musicVolume}[music]`, // Lower music volume
        '[0:a][music]amix=inputs=2:duration=first:dropout_transition=2[out]' // Mix
      ])
      .map('[out]')
      .audioCodec('libmp3lame')
      .audioBitrate('192k')
      .on('end', () => {
        console.log(`✅ Mixed audio saved: ${outputPath}`);
        resolve(outputPath);
      })
      .on('error', (err) => {
        console.error('FFmpeg mix error:', err);
        reject(err);
      })
      .save(outputPath);
  });
}

/**
 * Get audio file duration in seconds
 */
export async function getAudioDuration(filePath) {
  return new Promise((resolve, reject) => {
    ffmpeg.ffprobe(filePath, (err, metadata) => {
      if (err) {
        reject(err);
      } else {
        const duration = Math.floor(metadata.format.duration);
        resolve(duration);
      }
    });
  });
}

/**
 * Download background music from URL or use local file
 */
export async function downloadMusic(tempDir) {
  const localMusicPath = path.join(process.cwd(), 'assets', 'background_music.mp3');

  // Check if local file exists
  if (fs.existsSync(localMusicPath)) {
    console.log(`✅ Using local music: ${localMusicPath}`);
    return localMusicPath;
  }

  // Download from URL
  const musicUrl = process.env.BACKGROUND_MUSIC_URL;

  if (!musicUrl) {
    console.warn('⚠️ No background music URL provided, skipping music');
    return null;
  }

  const musicPath = path.join(tempDir, 'background_music.mp3');

  if (fs.existsSync(musicPath)) {
    console.log('✅ Music already downloaded');
    return musicPath;
  }

  console.log(`🎵 Downloading music from: ${musicUrl}`);

  const response = await axios.get(musicUrl, { responseType: 'stream' });
  const writer = fs.createWriteStream(musicPath);

  response.data.pipe(writer);

  return new Promise((resolve, reject) => {
    writer.on('finish', () => {
      console.log(`✅ Music downloaded: ${musicPath}`);
      resolve(musicPath);
    });
    writer.on('error', reject);
  });
}

/**
 * Add fade in/out effects
 */
export async function addFadeEffects(inputPath, outputPath, fadeInDuration = 2, fadeOutDuration = 3) {
  return new Promise((resolve, reject) => {
    ffmpeg(inputPath)
      .audioFilters([
        `afade=t=in:st=0:d=${fadeInDuration}`,
        `afade=t=out:st=0:d=${fadeOutDuration}`
      ])
      .on('end', () => resolve(outputPath))
      .on('error', reject)
      .save(outputPath);
  });
}

/**
 * Normalize audio volume
 */
export async function normalizeAudio(inputPath, outputPath) {
  const command = `ffmpeg -i "${inputPath}" -af loudnorm=I=-16:TP=-1.5:LRA=11 "${outputPath}"`;

  try {
    await execAsync(command);
    return outputPath;
  } catch (error) {
    console.error('Normalization error:', error.stderr);
    throw error;
  }
}
