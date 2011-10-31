<?php

/**
 * This is the category-action
 *
 * @author Lester Lievens <lester@netlash.com>
 */
class FrontendFaqCategory extends FrontendBaseBlock
{
	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->parse();
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		// assign questions
		$this->tpl->assign('faqQuestions', FrontendFaqModel::getQuestions((int) $this->data['id']));
	}
}
