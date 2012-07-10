<?php

/**
 * Mark a comment as inappropriate.
 *
 * @author Jeroen Van den Bossche <jeroen.vandenbossche@wijs.be>
 */
class FrontendBlogAjaxMarkInappropriate extends FrontendBaseAJAXAction
{
	/**
	 * The date our cookie should expire.
	 *
	 * @var string
	 */
	const EXPIRY_DATE = '01-01-2999';

	/**
	 * Mark a comment as inappropriate and set the correct cookie.
	 */
	public function execute()
	{
		parent::execute();
		$id = SpoonFilter::getPostValue('id', null, 0);

		if($id === 0 || !FrontendBlogModel::existsComment($id))
		{
			$this->output(self::BAD_REQUEST);
		}

		FrontendBlogModel::markInappropriateComment($id);

		// update cookies.
		if(SpoonCookie::exists('flagged_comments'))
		{
			$commentIds = SpoonCookie::get('flagged_comments');
			if(!in_array($id, $commentIds))
			{
				$commentIds[] = $id;
				SpoonCookie::set('flagged_comments', $commentIds, strtotime(self::EXPIRY_DATE));
			}
		}

		// create new cookie.
		else
		{
			$commentIds = array($id);
			SpoonCookie::set('flagged_comments', $commentIds, strtotime(self::EXPIRY_DATE));
		}

		$this->output(self::OK, null, FL::msg('MarkedAsInappropriate'));
	}
}
