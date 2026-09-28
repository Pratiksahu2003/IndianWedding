<?php

namespace Tests\Unit;

use App\Support\YoutubeEmbed;
use PHPUnit\Framework\TestCase;

class YoutubeEmbedTest extends TestCase
{
    public function test_extracts_video_id_from_watch_url(): void
    {
        $this->assertSame(
            'dQw4w9WgXcQ',
            YoutubeEmbed::videoId('https://www.youtube.com/watch?v=dQw4w9WgXcQ')
        );
    }

    public function test_extracts_video_id_from_short_url(): void
    {
        $this->assertSame(
            'dQw4w9WgXcQ',
            YoutubeEmbed::videoId('https://youtu.be/dQw4w9WgXcQ')
        );
    }

    public function test_extracts_video_id_from_shorts_url(): void
    {
        $this->assertSame(
            'dQw4w9WgXcQ',
            YoutubeEmbed::videoId('https://www.youtube.com/shorts/dQw4w9WgXcQ')
        );
    }

    public function test_builds_privacy_embed_url(): void
    {
        $this->assertSame(
            'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            YoutubeEmbed::embedUrl('https://youtu.be/dQw4w9WgXcQ')
        );
    }

    public function test_rejects_invalid_urls(): void
    {
        $this->assertNull(YoutubeEmbed::videoId('https://example.com/video'));
        $this->assertFalse(YoutubeEmbed::isValid('not-a-youtube-link'));
        $this->assertTrue(YoutubeEmbed::isValid(''));
    }
}
