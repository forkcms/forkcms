<?php

/**
 * Alter the sequence of a featured blog article.
 *
 * @author Jeroen Van den Bossche <jeroen.vandenbossche@wijs.be>
 */
class BackendBlogAjaxAlterFeaturedSequence extends BackendBaseAJAXAction
{
	/**
	 * Collect the new sequence and update the featured articles.
	 */
	public function execute()
	{
		parent::execute();
		$newIdSequence = trim(SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));
		$ids = (array) explode(',', rtrim($newIdSequence, ','));

		foreach($ids as $i => $id)
		{
			$item['sequence'] = $i + 1;
			BackendBlogModel::updateSequence($id, $item);
		}

		$this->output(self::OK, null, 'sequence updated');
	}
}
