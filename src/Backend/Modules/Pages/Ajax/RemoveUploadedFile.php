<?php

namespace Backend\Modules\Pages\Ajax;

use Symfony\Component\Filesystem\Filesystem;
use Backend\Core\Engine\Base\AjaxAction;
use Symfony\Component\HttpFoundation\Response;

/**
 * This action will allow you to cleanup files that were previously uploaded.
 */
class RemoveUploadedFile extends AjaxAction
{
    private const ALLOWED_DIRECTORIES = ['UserTemplate'];

    public function execute(): void
    {
        parent::execute();

        $request = $this->getRequest();
        if (!$request->request->has('file') || !$request->request->has('type')) {
            $this->output(Response::HTTP_BAD_REQUEST, 'Missing data');

            return;
        }

        $file = pathinfo($this->getRequest()->request->get('file'), PATHINFO_BASENAME);
        $directory = $this->getRequest()->request->get('type');
        if (!in_array($directory, self::ALLOWED_DIRECTORIES, true)) {
            $this->output(Response::HTTP_BAD_REQUEST, 'Invalid type');

            return;
        }

        $path = FRONTEND_FILES_PATH . '/Pages/' . $directory . '/' . $file;

        $filesystem = new Filesystem();
        if ($filesystem->exists($path)) {
            $filesystem->remove($path);
        }

        $this->output(Response::HTTP_OK);
    }
}
