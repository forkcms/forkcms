<?php

namespace ForkCMS\Tests\BlockEditor;

use Common\BlockEditor\Blocks\HeaderBlock;
use Common\BlockEditor\EditorBlocks;
use PHPUnit\Framework\TestCase;

class EditorBlocksTest extends TestCase
{
    public function testCreateJsonFromHtmlEmptyLineIsIgnored(): void
    {
        self::assertNull(EditorBlocks::createJsonFromHtml("\t\n\r\0\x0B "));
        self::assertNull(EditorBlocks::createJsonFromHtml(' '));
        self::assertNull(EditorBlocks::createJsonFromHtml('     '));
        self::assertNull(EditorBlocks::createJsonFromHtml(PHP_EOL));
    }

    public function testCreateJsonFromHtmlForbiddenElementsAreIgnored(): void
    {
        self::assertNull(EditorBlocks::createJsonFromHtml('<div></div>'));
        self::assertNull(EditorBlocks::createJsonFromHtml('<img src="example.com/img.png" />'));
        self::assertNull(EditorBlocks::createJsonFromHtml('<form></form>'));
    }

    public function testCreateJsonFromHtmlLooseTextGetsPlacedInAParagraph(): void
    {
        $baseTestString = 'Hello world';
        $testStrings = [
            $baseTestString,
            '<div>' . $baseTestString . '</div>',
        ];

        foreach ($testStrings as $testString) {
            $config = json_decode(EditorBlocks::createJsonFromHtml($testString), true);
            self::assertEquals('paragraph', $config['blocks'][0]['type']);
            self::assertEquals($baseTestString, $config['blocks'][0]['data']['text']);
        }
    }

    public function testCreateJsonFromHtmlCreatesParagraphForPTag(): void
    {
        $config = json_decode(EditorBlocks::createJsonFromHtml('<p>Hello world</p>'), true);

        self::assertEquals('paragraph', $config['blocks'][0]['type']);
        self::assertEquals('Hello world', $config['blocks'][0]['data']['text']);
    }

    public function testCreateJsonFromHtmlCreatesParagraphForITag(): void
    {
        $html = '<i>Hello world</i>';

        $config = json_decode(EditorBlocks::createJsonFromHtml($html), true);

        self::assertEquals('paragraph', $config['blocks'][0]['type']);
        self::assertEquals($html, $config['blocks'][0]['data']['text']);
    }

    public function testCreateJsonFromHtmlCreatesParagraphForBTag(): void
    {
        $html = '<b>Hello world</b>';

        $config = json_decode(EditorBlocks::createJsonFromHtml($html), true);

        self::assertEquals('paragraph', $config['blocks'][0]['type']);
        self::assertEquals($html, $config['blocks'][0]['data']['text']);
    }

    public function testCreateJsonFromHtmlCreatesParagraphForUTag(): void
    {
        $html = '<u>Hello world</u>';

        $config = json_decode(EditorBlocks::createJsonFromHtml($html), true);

        self::assertEquals('paragraph', $config['blocks'][0]['type']);
        self::assertEquals($html, $config['blocks'][0]['data']['text']);
    }

    public function testCreateJsonFromHtmlCreatesParagraphForATag(): void
    {
        $html = '<a href="https://www.fork-cms.com">Hello world</a>';

        $config = json_decode(EditorBlocks::createJsonFromHtml($html), true);

        self::assertEquals('paragraph', $config['blocks'][0]['type']);
        self::assertEquals($html, $config['blocks'][0]['data']['text']);
    }

    public function testCreateJsonFromHtmlCreatesSeparateBlockPerParagraph(): void
    {
        $config = json_decode(EditorBlocks::createJsonFromHtml('<p>Hello</p><p>world</p>'), true);

        self::assertEquals('paragraph', $config['blocks'][0]['type']);
        self::assertEquals('Hello', $config['blocks'][0]['data']['text']);
        self::assertEquals('paragraph', $config['blocks'][1]['type']);
        self::assertEquals('world', $config['blocks'][1]['data']['text']);
    }

    public function testCreateJsonFromHtmlCreatesBlocksForHeaders(): void
    {
        $config = json_decode(
            EditorBlocks::createJsonFromHtml(
                '<h1>level 1</h1><h2>level 2</h2><h3>level 3</h3><h4>level 4</h4><h5>level 5</h5><h6>level 6</h6>'
            ),
            true
        );

        foreach ($config['blocks'] as $index => $block) {
            $level = $index + 1;
            self::assertEquals(HeaderBlock::class, $config['blocks'][$index]['type']);
            self::assertEquals('level ' . $level, $config['blocks'][$index]['data']['text']);
            self::assertEquals($level, $config['blocks'][$index]['data']['level']);
        }
    }
}
