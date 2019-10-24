<?php

namespace Common\BlockEditor;

use Common\BlockEditor\Blocks\EditorBlock;

final class EditorBlocks
{
    /** @var array */
    private $config;

    /** @var array */
    private $validation;

    /** @var array */
    private $javaScriptUrls;

    public function __construct(EditorBlock ...$editorBlocks)
    {
        $this->config = [];
        $this->validation = [];
        $this->javaScriptUrls = [];

        $this->configureBlocks(...$editorBlocks);
    }

    public function configureBlocks(EditorBlock ...$editorBlocks): self
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

    public function getValidation(): array
    {
        return $this->validation;
    }

    public function getJavaScriptUrls(): array
    {
        return $this->javaScriptUrls;
    }
}
