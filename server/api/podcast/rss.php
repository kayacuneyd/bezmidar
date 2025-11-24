<?php
/**
 * Podcast RSS Feed (iTunes/Spotify compatible)
 * Used for podcast distribution to Spotify, Apple Podcasts, etc.
 */

require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/rss+xml; charset=utf-8');

try {
    // Fetch published episodes
    $stmt = $pdo->prepare("
        SELECT
            id,
            slug,
            title,
            description,
            audio_url,
            cover_image_url,
            duration_seconds,
            publish_date,
            created_at
        FROM podcast_episodes
        WHERE is_published = 1 AND processing_status = 'completed' AND audio_url IS NOT NULL
        ORDER BY publish_date DESC, created_at DESC
        LIMIT 100
    ");
    $stmt->execute();
    $episodes = $stmt->fetchAll();

    // Podcast metadata
    $podcastTitle = 'Dijital Mentor Podcast';
    $podcastDescription = 'Almanya\'daki Türk veliler için eğitim, teknoloji ve dijital dünyada çocuklarınızın başarısı hakkında bilgiler.';
    $podcastLink = 'https://dijitalmentor.de/podcast';
    $podcastImage = 'https://dijitalmentor.de/logo.png';
    $podcastAuthor = 'Dijital Mentor';
    $podcastEmail = 'info@dijitalmentor.de';
    $podcastCategory = 'Education';
    $podcastLanguage = 'tr';

    // Build RSS feed
    // Clean output buffer to remove any whitespace or output before XML
    if (ob_get_length()) ob_clean();

    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;

    // RSS root
    $rss = $xml->createElement('rss');
    $rss->setAttribute('version', '2.0');
    $rss->setAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
    $rss->setAttribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
    $rss->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
    $xml->appendChild($rss);

    // Channel
    $channel = $xml->createElement('channel');
    $rss->appendChild($channel);

    // Atom self link
    $atomLink = $xml->createElement('atom:link');
    $atomLink->setAttribute('href', 'https://dijitalmentor.de/podcast/feed.xml');
    $atomLink->setAttribute('rel', 'self');
    $atomLink->setAttribute('type', 'application/rss+xml');
    $channel->appendChild($atomLink);

    // Channel metadata
    $channel->appendChild($xml->createElement('title', htmlspecialchars($podcastTitle)));
    $channel->appendChild($xml->createElement('link', $podcastLink));
    $channel->appendChild($xml->createElement('description', htmlspecialchars($podcastDescription)));
    $channel->appendChild($xml->createElement('language', $podcastLanguage));
    $channel->appendChild($xml->createElement('copyright', '© ' . date('Y') . ' Dijital Mentor'));
    $channel->appendChild($xml->createElement('lastBuildDate', date(DATE_RFC2822)));

    // iTunes-specific tags
    $itunesAuthor = $xml->createElement('itunes:author', htmlspecialchars($podcastAuthor));
    $channel->appendChild($itunesAuthor);

    $itunesSummary = $xml->createElement('itunes:summary', htmlspecialchars($podcastDescription));
    $channel->appendChild($itunesSummary);

    $itunesOwner = $xml->createElement('itunes:owner');
    $itunesOwner->appendChild($xml->createElement('itunes:name', htmlspecialchars($podcastAuthor)));
    $itunesOwner->appendChild($xml->createElement('itunes:email', $podcastEmail));
    $channel->appendChild($itunesOwner);

    $itunesImage = $xml->createElement('itunes:image');
    $itunesImage->setAttribute('href', $podcastImage);
    $channel->appendChild($itunesImage);

    $itunesCategory = $xml->createElement('itunes:category');
    $itunesCategory->setAttribute('text', $podcastCategory);
    $channel->appendChild($itunesCategory);

    $itunesExplicit = $xml->createElement('itunes:explicit', 'no');
    $channel->appendChild($itunesExplicit);

    // RSS image
    $image = $xml->createElement('image');
    $image->appendChild($xml->createElement('url', $podcastImage));
    $image->appendChild($xml->createElement('title', htmlspecialchars($podcastTitle)));
    $image->appendChild($xml->createElement('link', $podcastLink));
    $channel->appendChild($image);

    // Episodes
    foreach ($episodes as $episode) {
        $item = $xml->createElement('item');

        // Title
        $item->appendChild($xml->createElement('title', htmlspecialchars($episode['title'])));

        // Link
        $episodeLink = "https://dijitalmentor.de/podcast/{$episode['slug']}";
        $item->appendChild($xml->createElement('link', $episodeLink));

        // GUID
        $guid = $xml->createElement('guid', $episodeLink);
        $guid->setAttribute('isPermaLink', 'true');
        $item->appendChild($guid);

        // Description
        if ($episode['description']) {
            $item->appendChild($xml->createElement('description', htmlspecialchars($episode['description'])));
        }

        // Pub date
        $pubDate = strtotime($episode['publish_date'] ?: $episode['created_at']);
        $item->appendChild($xml->createElement('pubDate', date(DATE_RFC2822, $pubDate)));

        // Enclosure (audio file)
        $audioUrl = $episode['audio_url'];
        if (!str_starts_with($audioUrl, 'http')) {
            $audioUrl = getenv('CLOUDFLARE_R2_PUBLIC_URL') . '/' . $audioUrl;
        }

        $enclosure = $xml->createElement('enclosure');
        $enclosure->setAttribute('url', $audioUrl);
        $enclosure->setAttribute('type', 'audio/mpeg');

        // Get file size (optional, can be omitted)
        $enclosure->setAttribute('length', '0'); // Placeholder, can fetch actual size if needed

        $item->appendChild($enclosure);

        // iTunes duration
        if ($episode['duration_seconds']) {
            $duration = formatDuration($episode['duration_seconds']);
            $item->appendChild($xml->createElement('itunes:duration', $duration));
        }

        // iTunes episode image
        if ($episode['cover_image_url']) {
            $coverUrl = $episode['cover_image_url'];
            if (!str_starts_with($coverUrl, 'http')) {
                $coverUrl = getenv('CLOUDFLARE_R2_PUBLIC_URL') . '/' . $coverUrl;
            }

            $episodeImage = $xml->createElement('itunes:image');
            $episodeImage->setAttribute('href', $coverUrl);
            $item->appendChild($episodeImage);
        }

        $channel->appendChild($item);
    }

    echo $xml->saveXML();

} catch (Throwable $e) {
    error_log('RSS feed error: ' . $e->getMessage());
    http_response_code(500);
    echo '<?xml version="1.0" encoding="UTF-8"?><error>RSS feed could not be generated</error>';
}

function formatDuration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;

    if ($hours > 0) {
        return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
    }
    return sprintf('%d:%02d', $minutes, $secs);
}
