<?php

if (! function_exists('gym_normalizar_video_url')) {
    /** URL lista para abrir (YouTube, Vimeo o link directo). */
    function gym_normalizar_video_url(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }
        if (! preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }
        if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/shorts/)([\w-]{11})#i', $url, $m)) {
            return 'https://www.youtube.com/watch?v=' . $m[1];
        }

        return $url;
    }
}
