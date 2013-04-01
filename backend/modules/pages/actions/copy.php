<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * BackendPagesCopy
 * This is the copy-action, it will copy pages from one language to another
 * @remark:	IMPORTANT existing data will be removed, this feature is also experimental!
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Sam Tubbax <sam@sumocoders.be>
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class BackendPagesCopy extends BackendBaseActionDelete
{
	/**
	 * The languages
	 *
	 * @var string
	 */
	private $from, $to;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$this->from = $this->getParameter('from');
		$this->to = $this->getParameter('to');

		// validate
		if($this->from == '') throw new BackendException('Specify a from-parameter.');
		if($this->to == '') throw new BackendException('Specify a to-parameter.');

		// copy pages
		BackendPagesModel::copy($this->from, $this->to);

		// redirect
		$this->redirect(BackendModel::createURLForAction('index') . '&report=copy-added&var=' . urlencode($this->to));
	}
}
