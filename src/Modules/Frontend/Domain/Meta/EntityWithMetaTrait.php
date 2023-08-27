<?php

namespace ForkCMS\Modules\Frontend\Domain\Meta;

use Doctrine\ORM\Mapping as ORM;

trait EntityWithMetaTrait
{
    #[ORM\OneToOne(targetEntity: Meta::class, cascade: ["persist", "remove"])]
    #[ORM\JoinColumn(name: "meta_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private Meta $meta;

    public function getMeta(): Meta
    {
        return $this->meta;
    }
}
