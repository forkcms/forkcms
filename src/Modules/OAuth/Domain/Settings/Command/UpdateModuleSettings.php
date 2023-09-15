<?php

namespace ForkCMS\Modules\OAuth\Domain\Settings\Command;

use Symfony\Component\Validator\Constraints\Expression;

class UpdateModuleSettings
{
    public function __construct(
        #[Expression(expression: '!this.enabled || this.clientId !== ""', message: 'err.FieldIsRequired')]
        public ?string $clientId = null,
        #[Expression(expression: '!this.enabled || this.clientSecret !== ""', message: 'err.FieldIsRequired')]
        public ?string $clientSecret = null,
        #[Expression(expression: '!this.enabled || this.tenant !== ""', message: 'err.FieldIsRequired')]
        public ?string $tenant = null,
        public bool $enabled = false,
    ) {
    }
}
