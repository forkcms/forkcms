<?php
/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview of blog posts
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class BackendTwigBootstrap extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$templateFile = __DIR__ . '/../layout/templates/twig_bootstrap.tpl';

		$this->display($templateFile);
	}

	public function assignContent($content)
	{
		$this->tpl->assign('twigData', $content);
	}
}
