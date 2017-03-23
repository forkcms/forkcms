<?php

namespace Backend\Modules\Pages\Ajax;

use Symfony\Component\Filesystem\Filesystem;
use Backend\Core\Engine\Base\AjaxAction;

/**
 * This action will allow you to cleanup files that were previously uploaded.
 */
class RemoveUploadedFile extends AjaxAction
{
    public function execute()
    {
        $request = $this->get('request');
        if (!$request->request->has('file') || ! $request->request->has('type')) {
            return $this->output(self::BAD_REQUEST, 'Missing data');
        }

        $file = pathinfo($this->get('request')->request->get('file'), PATHINFO_BASENAME);
        $directory = $this->get('request')->request->get('type');

        $path = FRONTEND_FILES_PATH . '/' . $directory . '/' . $file;

        $filesystem = new Filesystem();
        if ($filesystem->exists($path)) {
            $filesystem->remove($path);
        }

        $this->output(self::OK);
    }
}
