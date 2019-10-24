<?php

namespace Common\BlockEditor;

use Common\BlockEditor\Blocks\EditorBlock;
use Doctrine\Common\Collections\ArrayCollection;

final class EditorBlocks
{
    /** @var ArrayCollection */
    private $blocks;

    /** @var array */
    private $config;

    /** @var array */
    private $validation;

    /** @var bool */
    private $allowUnConfiguredBlocks;

    public function __construct(bool $allowUnConfiguredBlocks = false, string ...$possibleBlocks)
    {
        $this->blocks = new ArrayCollection();
        $this->allowUnConfiguredBlocks = $allowUnConfiguredBlocks;
        $this->config = [];
        $this->validation = [];

        $this->configureBlocks($possibleBlocks);
    }

    public function addBlocks(EditorBlock ...$editorBlocks): self
    {
        foreach ($editorBlocks as $editorBlock) {
            if ($this->allowUnConfiguredBlocks && !array_key_exists($editorBlock::getName(), $this->config)) {
                $this->configureBlocks(get_class($editorBlock));
            }

            if (!$this->blocks->contains($editorBlock) && array_key_exists($editorBlock::getName(), $this->config)) {
                $this->blocks->add($editorBlock);
            }
        }

        return $this;
    }

    public function configureBlocks(string ...$editorBlockFQCNs): self
    {
        foreach ($editorBlockFQCNs as $editorBlockFQCN) {
            if (is_subclass_of($editorBlockFQCN, EditorBlock::class)) {
                $this->config[$editorBlockFQCN::getName()] = $editorBlockFQCN::getConfig();
                $this->validation[$editorBlockFQCN::getName()] = $editorBlockFQCN::getValidation();
            }
        }

        return $this;
    }

    /** @return EditorBlock[] */
    public function getBlocks(): array
    {
        return $this->blocks->toArray();
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getValidation(): array
    {
        return $this->validation;
    }
}
