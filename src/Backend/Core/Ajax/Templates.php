<?php

namespace Backend\Core\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Exception\RedirectException;
use Symfony\Component\Filesystem\Filesystem;
use Backend\Core\Engine\Base\AjaxAction;
use Symfony\Component\HttpFoundation\Response;

/**
 * This action will generate JS that represents the templates that will be available in CK Editor
 */
class Templates extends AjaxAction
{
    /** @var array */
    private $templates;

    /**
     * Execute the action
     */
    public function execute()
    {
        $this->templates = [];
        $theme = $this->get('fork.settings')->get('Core', 'theme');
        $files[] = BACKEND_PATH . '/Core/Layout/EditorTemplates/templates.js';
        $themePath = FRONTEND_PATH . '/Themes/' . $theme . '/Core/Layout/EditorTemplates/templates.js';

        if (is_file($themePath)) {
            $files[] = $themePath;
        }

        foreach ($files as $file) {
            $this->processFile($file);
        }
    }

    /**
     * @return Response
     */
    public function getContent()
    {
        return new Response(
            'CKEDITOR.addTemplates(\'default\', { imagesPath: \'/\', templates:' . "\n" . json_encode(
                $this->templates
            ) . "\n" . '});',
            Response::HTTP_OK,
            ['Content-type' => 'text/javascript']
        );
    }

    /**
     * Process the content of the file.
     *
     * @param string $file The file to process.
     */
    private function processFile($file)
    {
        $filesystem = new Filesystem();

        // if the files doesn't exists we can stop here
        if (!$filesystem->exists($file)) {
            return;
        }

        // fetch content from file
        $content = file_get_contents($file);
        $json = @json_decode($content, true);

        // skip invalid JSON
        if ($json === false || $json === null) {
            return;
        }

        // loop templates
        foreach ((array) $json as $template) {
            // skip items without a title
            if (!isset($template['title'])) {
                continue;
            }

            if (isset($template['file']) && $filesystem->exists(PATH_WWW . $template['file'])) {
                $template['html'] = file_get_contents(PATH_WWW . $template['file']);
            }

            // skip items without HTML
            if (!isset($template['html'])) {
                continue;
            }

            $image = '';
            if (isset($template['image'])) {
                // we have to remove the first slash, because that is set in the wrapper.
                // Otherwise the images don't work
                $image = ltrim($template['image'], '/');
            }

            $temp['title'] = $template['title'];
            $temp['description'] = isset($template['description']) ? $template['description'] : '';
            $temp['image'] = $image;
            $temp['html'] = $template['html'];

            // add the template
            $this->templates[] = $temp;
        }
    }
}
