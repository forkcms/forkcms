<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form to edit the mailing contents through an iframe
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendMailmotorEditMailingIframe extends BackendBaseActionEdit
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
		if(BackendMailmotorModel::existsMailing($this->id))
		{
			parent::execute();
			$this->getData();
			$this->parse();
			$this->display(BACKEND_MODULE_PATH . '/layout/templates/edit_mailing_iframe.tpl');
		}

		// no item found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
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
		if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
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
		// require the css to inline styles parser
		require 'external/css_to_inline_styles.php';

		// template content is empty
		if(!isset($this->template['content'])) $this->redirect(BackendModel::createURLForAction('edit') . '&id=' . $this->id . '&step=2&exclude_id=' . $this->id . '&error=template-does-not-exist');

		// set CSS object
		$css = new CSSToInlineStyles($this->template['content'], $this->template['css']);
		$HTML = urldecode($css->convert());

		/*
			I realise this is a bit confusing, so let me elaborate:

			1.	edit_mailing_iframe.tpl contains a var {$templateHtml}. This is where $this->template['content'] goes.

			2.	Inside $this->template['content'] should be a textarea with a variable {$contentHtml} inside. This will
				become the editor field which will contain our stored content HTML.

			3.	We need everything inside the <body> tags so we don't end up with two <body>s.
		*/

		// find the body element
		if(preg_match('/<body.*>.*?<\/body>/is', $HTML, $match))
		{
			// search values
			$search = array();
			$search[] = 'body';
			$search[] = '{$contentHtml}';
			$search[] = '{$siteURL}';
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
