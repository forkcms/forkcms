<?php

namespace ForkCMS\Bundle\CoreBundle\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

final class UrlValidator
{
    public function isExternalUrl(string $url): bool
    {
        $violations = Validation::createValidator()->validate(
            $url,
            [
                new Assert\Url(
                    [
                        'checkDNS' => true, // Just a crappy name to say that it will check the url has a valid hostname
                    ]
                ),
            ]
        );

        // if there are no violations the url is a valid external url
        return $violations->count() === 0;
    }
}
