<?php

namespace ForkCMS\Core\Domain\Header\FlashMessage;

enum FlashMessageType: string
{
    case INFO = 'info';
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case ERROR = 'error';
}
