<?php

/**
 * Reorder questions
 *
 * @package		backend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @since		2.1
 */
class BackendFaqAjaxSequenceQuestions extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$questionId = SpoonFilter::getPostValue('questionId', null, '', 'int');
		$fromCategoryId = SpoonFilter::getPostValue('fromCategoryId', null, '', 'int');
		$toCategoryId = SpoonFilter::getPostValue('toCategoryId', null, '', 'int');
		$fromCategorySequence = SpoonFilter::getPostValue('fromCategorySequence', null, '', 'string');
		$toCategorySequence = SpoonFilter::getPostValue('toCategorySequence', null, '', 'string');

		// invalid question id
		if(!BackendFaqModel::existsQuestion($questionId)) $this->output(self::BAD_REQUEST, null, 'question does not exist');

		// list ids
		$fromCategorySequence = (array) explode(',', ltrim($fromCategorySequence, ','));
		$toCategorySequence = (array) explode(',', ltrim($toCategorySequence, ','));

		// is the question moved to a new category?
		if($fromCategoryId != $toCategoryId)
		{
			// build item
			$item['id'] = $questionId;
			$item['category_id'] = $toCategoryId;

			// update the category
			BackendFaqModel::updateQuestion($item);

			// loop id's and set new sequence
			foreach($toCategorySequence as $i => $id)
			{
				// build item
				$item = array();
				$item['id'] = (int) $id;
				$item['sequence'] = $i + 1;

				// update sequence
				if(BackendFaqModel::existsQuestion($item['id'])) BackendFaqModel::updateQuestion($item);
			}
		}

		// loop id's and set new sequence
		foreach($fromCategorySequence as $i => $id)
		{
			// build item
			$item['id'] = (int) $id;
			$item['sequence'] = $i + 1;

			// update sequence
			if(BackendFaqModel::existsQuestion($item['id'])) BackendFaqModel::updateQuestion($item);
		}

		// success output
		$this->output(self::OK, null, 'sequence updated');
	}
}

?>