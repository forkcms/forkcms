<?php

namespace ForkCMS\Utility\Tests\Svg;

use ForkCMS\Utility\Svg\SvgSanitizer;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Regression test for the stored-XSS vulnerability (CWE-79) via uploaded svg files: an svg is served from the
 * site origin as an active document, so script elements and event handlers must never survive an upload.
 */
class SvgSanitizerTest extends TestCase
{
    private SvgSanitizer $svgSanitizer;

    /** @var string[] */
    private array $temporaryFiles = [];

    protected function setUp(): void
    {
        $this->svgSanitizer = new SvgSanitizer();
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $temporaryFile) {
            @unlink($temporaryFile);
        }

        $this->temporaryFiles = [];
    }

    public function testTheReportedProofOfConceptIsNeutralised(): void
    {
        // the payload from the security report: both an on* handler and an inline script
        $sanitized = $this->svgSanitizer->sanitize(
            '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="60"'
            . ' onload="document.documentElement.setAttribute(\'data-xss\', document.domain)">'
            . '<script>document.documentElement.setAttribute("data-xss-script", document.domain)</script>'
            . '<text x="5" y="30">svg</text>'
            . '</svg>'
        );

        self::assertStringNotContainsStringIgnoringCase('<script', $sanitized);
        self::assertStringNotContainsStringIgnoringCase('onload', $sanitized);
        self::assertStringNotContainsStringIgnoringCase('document.domain', $sanitized);
    }

    public function testLegitimateDrawingAndDimensionsSurvive(): void
    {
        // MediaItem::setResolutionForSvg reads the viewBox/width/height, so those have to stay intact
        $sanitized = $this->svgSanitizer->sanitize(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 40" width="120" height="40">'
            . '<path d="M0 0 L120 40" fill="#ff0000"/>'
            . '<text x="5" y="30">logo</text>'
            . '</svg>'
        );

        self::assertStringContainsString('viewBox="0 0 120 40"', $sanitized);
        self::assertStringContainsString('width="120"', $sanitized);
        self::assertStringContainsString('<path', $sanitized);
        self::assertStringContainsString('<text', $sanitized);
    }

    /**
     * @dataProvider provideDangerousSvgFiles
     */
    public function testDangerousConstructsAreStripped(string $svg, string $mayNotContain): void
    {
        self::assertStringNotContainsStringIgnoringCase(
            $mayNotContain,
            $this->svgSanitizer->sanitize($svg)
        );
    }

    public function provideDangerousSvgFiles(): array
    {
        return [
            'event handler on a child element' => [
                '<svg xmlns="http://www.w3.org/2000/svg"><circle r="10" onclick="alert(1)"/></svg>',
                'onclick',
            ],
            'javascript: in an xlink:href' => [
                '<svg xmlns="http://www.w3.org/2000/svg"><a xlink:href="javascript:alert(1)"><text>x</text></a></svg>',
                'javascript:',
            ],
            'script inside a foreignObject' => [
                '<svg xmlns="http://www.w3.org/2000/svg"><foreignObject><script>alert(1)</script></foreignObject></svg>',
                '<script',
            ],
            'animated attribute pointing at a script url' => [
                '<svg xmlns="http://www.w3.org/2000/svg"><a><animate attributeName="href" values="javascript:alert(1)"/>'
                . '<text>x</text></a></svg>',
                'javascript:',
            ],
            'external reference through a use element' => [
                '<svg xmlns="http://www.w3.org/2000/svg"><use href="data:image/svg+xml;base64,PHN2Zz48L3N2Zz4="/></svg>',
                '<use',
            ],
        ];
    }

    public function testEmptyContentIsRejected(): void
    {
        $this->expectException(RuntimeException::class);

        $this->svgSanitizer->sanitize('   ');
    }

    public function testExtensionDetectionIsCaseInsensitive(): void
    {
        self::assertTrue(SvgSanitizer::isSvgExtension('svg'));
        self::assertTrue(SvgSanitizer::isSvgExtension('SVG'));
        self::assertTrue(SvgSanitizer::isSvgExtension('Svg'));
        self::assertFalse(SvgSanitizer::isSvgExtension('png'));
        self::assertFalse(SvgSanitizer::isSvgExtension(''));
    }

    public function testSanitizeFileRewritesTheFileInPlace(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'fork_svg_') . '.svg';
        file_put_contents(
            $path,
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script><text>svg</text></svg>'
        );

        try {
            $this->svgSanitizer->sanitizeFile($path);

            $contents = file_get_contents($path);
            self::assertStringNotContainsStringIgnoringCase('<script', $contents);
            self::assertStringContainsString('<text', $contents);
        } finally {
            @unlink($path);
        }
    }

    public function testSanitizeFileThrowsForAMissingFile(): void
    {
        $this->expectException(RuntimeException::class);

        $this->svgSanitizer->sanitizeFile(sys_get_temp_dir() . '/this-file-does-not-exist.svg');
    }

    /**
     * An svg uploaded as "logo.png" passes both upload allowlists: the extension is checked against the list of
     * allowed extensions and the mime type against the list of allowed mime types, never against each other. The
     * file name therefore can't decide whether the bytes have to be sanitized.
     */
    public function testSvgContentIsRecognisedWhateverTheFileIsCalled(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" onload="alert(1)"><text>x</text></svg>';

        self::assertTrue(SvgSanitizer::isSvgContent($svg));
        self::assertTrue(SvgSanitizer::isSvgFile($this->writeTemporaryFile($svg, 'png')));
        self::assertTrue(SvgSanitizer::isSvgFile($this->writeTemporaryFile($svg, 'svg')));
    }

    public function testOtherContentIsNotRecognisedAsAnSvg(): void
    {
        // a one pixel png and a pdf header, the kind of upload that must not be handed to the sanitizer: it
        // parses svg as xml, so it would reject them
        $png = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        );

        self::assertFalse(SvgSanitizer::isSvgContent($png));
        self::assertFalse(SvgSanitizer::isSvgContent('%PDF-1.4'));
        self::assertFalse(SvgSanitizer::isSvgFile($this->writeTemporaryFile($png, 'png')));
    }

    private function writeTemporaryFile(string $content, string $extension): string
    {
        $path = tempnam(sys_get_temp_dir(), 'fork_svg_') . '.' . $extension;
        file_put_contents($path, $content);

        $this->temporaryFiles[] = $path;

        return $path;
    }
}
