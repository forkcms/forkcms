<?php

namespace Common\BlockEditor;

use Common\BlockEditor\Blocks\AbstractBlock;
use Common\BlockEditor\Blocks\HeaderBlock;
use DOMDocument;
use EditorJS\EditorJS;
use EditorJS\EditorJSException;

final class EditorBlocks
{
    /** @var array */
    private $config;

    /** @var array */
    private $validation;

    /** @var array */
    private $javaScriptUrls;

    public function __construct(AbstractBlock ...$editorBlocks)
    {
        $this->config = [];
        $this->validation = [];
        $this->javaScriptUrls = [];

        $this->configureBlocks(...$editorBlocks);
    }

    public function configureBlocks(AbstractBlock ...$editorBlocks): self
    {
        foreach ($editorBlocks as $editorBlock) {
            $this->config[$editorBlock->getName()] = $editorBlock->getConfig();
            $this->validation[$editorBlock->getName()] = $editorBlock->getValidation();
            $javaScriptUrl = $editorBlock->getJavaScriptUrl();
            if ($javaScriptUrl !== null) {
                $this->javaScriptUrls[$javaScriptUrl] = $javaScriptUrl;
            }
        }

        return $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function isValid(string $json): bool
    {
        try {
            new EditorJS($json, json_encode(['tools' => $this->validation]));
        } catch (EditorJSException $editorJSException) {
            return false;
        }

        return true;
    }

    public function getJavaScriptUrls(): array
    {
        return $this->javaScriptUrls;
    }

    public static function createJsonFromHtml(string $html): ?string
    {
        $emptyCharacter = "\t\n\r\0\x0B ";
        $allowedTextTags = '<i><b><u><a>';
        $html = strip_tags(trim($html, $emptyCharacter), '<h1><h2><h3><h4><h5><h6><p>' . $allowedTextTags);

        if (empty($html)) {
            return null;
        }

        $data = [
            'time' => time(),
            'version' => '0.0.0',
            'blocks' => [],
        ];

        $dom = new DOMDocument();
        $dom->loadHTML(strip_tags($html, '<h1><h2><h3><h4><h5><h6><p>' . $allowedTextTags));

        foreach ($dom->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $text = strip_tags(trim($dom->saveHTML($node), $emptyCharacter), $allowedTextTags);
            if ($node->nodeType !== XML_ELEMENT_NODE && empty($text)) {
                continue;
            }

            $elementConfig = [
                'type' =>  $node->nodeName[0] === 'h' ? HeaderBlock::class : 'paragraph',
                'data' => [
                    'text' => $text,
                ],
            ];

            if ($elementConfig['type'] === HeaderBlock::class) {
                $elementConfig['data']['level'] = (int) $node->nodeName[1];
            }

            $data['blocks'][] = $elementConfig;
        }

        return json_encode($data);
    }
}
