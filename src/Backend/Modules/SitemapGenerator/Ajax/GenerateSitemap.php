<?php

namespace Backend\Modules\SitemapGenerator\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Modules\SitemapGenerator\Domain\Command\GenerateSitemap as GenerateSitemapCommand;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class GenerateSitemap extends AjaxAction
{
    public function execute(): void
    {
        parent::execute();

        try {
            $this->get('command_bus')->handle(new GenerateSitemapCommand());
        } catch (Exception $exception) {
            $this->output(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                [],
                $exception->getMessage()
            );
        }

        $this->output(Response::HTTP_OK);
    }
}
