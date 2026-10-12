<?php

namespace ForkCMS\Utility\Svg;

use finfo;
use Rhukster\DomSanitizer\DOMSanitizer;
use RuntimeException;
use Symfony\Component\Mime\MimeTypes;

/**
 * Strips active content out of uploaded SVG files.
 *
 * An SVG is an active document: a <script> element or an on* handler inside one executes JavaScript when the file
 * is opened as a document (direct navigation, or an <iframe>/<object>/<embed>). Because uploads are stored under
 * the web root and served straight off disk, an unsanitized SVG is stored XSS in the site origin.
 *
 * Sanitizing the bytes is done on write, so it also covers the upload paths where the mime type is never checked.
 */
final class SvgSanitizer
{
    private const SVG_MIME_TYPES = ['image/svg', 'image/svg+xml'];

    public static function isSvgExtension(string $extension): bool
    {
        return strtolower($extension) === 'svg';
    }

    public static function isSvgFile(string $path): bool
    {
        return self::isSvgExtension(pathinfo($path, PATHINFO_EXTENSION))
            || in_array((new MimeTypes())->guessMimeType($path), self::SVG_MIME_TYPES, true);
    }

    public static function isSvgContent(string $content): bool
    {
        return in_array((new finfo(FILEINFO_MIME_TYPE))->buffer($content), self::SVG_MIME_TYPES, true);
    }

    /**
     * @throws RuntimeException When the content could not be parsed as an SVG document
     */
    public function sanitize(string $content): string
    {
        if (trim($content) === '') {
            throw new RuntimeException('The SVG file is empty.');
        }

        $sanitized = (new DOMSanitizer(DOMSanitizer::SVG))->sanitize($content);

        if (!is_string($sanitized) || trim($sanitized) === '') {
            throw new RuntimeException('The SVG file could not be sanitized.');
        }

        return $sanitized;
    }

    /**
     * Sanitizes a file in place.
     *
     * @throws RuntimeException When the file can not be read, sanitized or written back
     */
    public function sanitizeFile(string $path): void
    {
        $content = @file_get_contents($path);

        if ($content === false) {
            throw new RuntimeException('Could not read the SVG file "' . $path . '".');
        }

        if (@file_put_contents($path, $this->sanitize($content)) === false) {
            throw new RuntimeException('Could not write the sanitized SVG file "' . $path . '".');
        }
    }
}
