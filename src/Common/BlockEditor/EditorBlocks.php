<?php

namespace Common\BlockEditor;

use Common\BlockEditor\Blocks\AbstractBlock;
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
}
