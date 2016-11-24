<?php

namespace Backend\Modules\Mailmotor\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language as BackendLanguage;
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;

/**
 * This is the edit-action, it will display a form to edit the mailing contents through an iframe
 */
class EditMailingIframe extends BackendBaseActionEdit
{
    /**
     * The active template
     *
     * @return array
     */
    private $template;

    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if (BackendMailmotorModel::existsMailing($this->id)) {
            $this->addAssets();
            parent::execute();
            $this->getData();
            $this->parse();
            $this->display(BACKEND_MODULES_PATH . '/Mailmotor/Layout/Templates/EditMailingIframe.html.twig');
            if ($this->getContainer()->has('profiler')) {
                $this->get('profiler')->disable();
            }
        } else {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }

    /**
     * @return Response
     */
    public function getContent()
    {
        return new Response(
            $this->content,
            200,
            ['X-Frame-Options' => 'sameorigin']
        );
    }

    /**
     * Adds required assets
     */
    private function addAssets()
    {
        // we add iframe css
        $this->header->addCSS('Iframe.css', 'Mailmotor');

        // we add JS because we need CKEditor
        $this->header->addJS('ckeditor/ckeditor.js', 'Core', false);
        $this->header->addJS('ckeditor/adapters/jquery.js', 'Core', false);
        $this->header->addJS('ckfinder/ckfinder.js', 'Core', false);

        // add the internal link lists-file
        if (is_file(FRONTEND_CACHE_PATH . '/Navigation/editor_link_list_' . BackendLanguage::getWorkingLanguage() . '.js')) {
            $timestamp = @filemtime(
                FRONTEND_CACHE_PATH . '/Navigation/editor_link_list_' . BackendLanguage::getWorkingLanguage() . '.js'
            );
            $this->header->addJS(
                '/src/Frontend/Cache/Navigation/editor_link_list_' . BackendLanguage::getWorkingLanguage(
                ) . '.js?m=' . $timestamp,
                null,
                false,
                true,
                false
            );
        }
    }

    /**
     * Get the data
     */
    private function getData()
    {
        // get the record
        $this->record = (array) BackendMailmotorModel::getMailing($this->id);

        // get the template record for this mailing
        $this->template = BackendMailmotorModel::getTemplate($this->record['language'], $this->record['template']);

        // no item found, throw an exceptions, because somebody is fucking with our URL
        if (empty($this->record)) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        // assign the active record and additional variables
        $this->tpl->assign('mailing', $this->record);
        $this->tpl->assign('template', $this->template);

        // parse template content
        $this->parseTemplateContent();
    }

    /**
     * Parse the content editor
     */
    private function parseTemplateContent()
    {
        // template content is empty
        if (!isset($this->template['content'])) {
            $this->redirect(
                BackendModel::createURLForAction(
                    'Edit'
                ) . '&id=' . $this->id . '&step=2&exclude_id=' . $this->id . '&error=template-does-not-exist'
            );
        }

        // set CSS object
        $css = new CSSToInlineStyles($this->template['content'], $this->template['css']);
        $HTML = urldecode($css->convert());

        /*
            I realise this is a bit confusing, so let me elaborate:

            1. EditMailingIframe.html.twig contains a var {$templateHtml}.
               This is where $this->template['content'] goes.

            2. Inside $this->template['content'] should be a textarea with a variable {$contentHtml} inside.
               This will become the editor field which will contain our stored content HTML.

            3. We need everything inside the <body> tags so we don't end up with two <body>s.
        */

        // find the body element
        if (preg_match('/<body.*>.*?<\/body>/is', $HTML, $match)) {
            // search values
            $search = array();
            $search[] = 'body';
            $search[] = '{{ contentHtml|raw }}';
            $search[] = '{{ siteURL }}';
            $search[] = '&quot;';

            // replace values
            $replace = array();
            $replace[] = 'div';
            $replace[] = $this->record['content_html'];
            $replace[] = SITE_URL;
            $replace[] = '"';

            // replace
            $HTML = str_replace($search, $replace, $match[0]);
        }

        // parse the inline styles
        $this->tpl->assign('templateHtml', $HTML);
    }
}
