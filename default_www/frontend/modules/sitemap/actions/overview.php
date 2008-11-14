<?php
class SitemapOverview extends FrontendBaseAction
{
	public function execute()
	{
//		throw new FrontendException('Hello', 12);

		$this->tpl->assign('sitemap', 'Hallo bram');
	}
}
?>