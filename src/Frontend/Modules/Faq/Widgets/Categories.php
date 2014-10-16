<?php

namespace Frontend\Modules\Faq\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\Faq\Engine\Model as FrontendFaqModel;

/**
 * This is a widget with faq categories
 *
 * @author Jeroen Desloovere <info@jeroendesloovere.be>
 */
class Categories extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 */
	public function execute()
	{
		// call parent
		parent::execute();

		$this->loadTemplate();
		$this->parse();
	}

	/**
	 * Parse
	 */
	private function parse()
	{
		$this->tpl->assign('widgetFaqCategories', FrontendFaqModel::getCategories());
	}
}
