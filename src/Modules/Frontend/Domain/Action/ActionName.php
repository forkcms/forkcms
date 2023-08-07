<?php

namespace ForkCMS\Modules\Frontend\Domain\Action;

use ForkCMS\Core\Domain\Identifier\NamedIdentifier;
use ForkCMS\Modules\Frontend\Domain\Block\BlockName;
use ForkCMS\Modules\Frontend\Domain\Block\Type;
use Stringable;

final class ActionName implements Stringable, BlockName
{
    use NamedIdentifier;

    public function getType(): Type
    {
        return Type::ACTION;
    }
}
