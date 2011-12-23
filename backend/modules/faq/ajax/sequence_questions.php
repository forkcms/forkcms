<?php

/**
 * Reorder questions
 *
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendFaqAjaxSequenceQuestions extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$questionId = SpoonFilter::getPostValue('questionId', null, '', 'int');
		$fromCategoryId = SpoonFilter::getPostValue('fromCategoryId', null, '', 'int');
		$toCategoryId = SpoonFilter::getPostValue('toCategoryId', null, '', 'int');
		$fromCategorySequence = SpoonFilter::getPostValue('fromCategorySequence', null, '', 'string');
		$toCategorySequence = SpoonFilter::getPostValue('toCategorySequence', null, '', 'string');

		// invalid question id
		if(!BackendFaqModel::exists($questionId)) $this->output(self::BAD_REQUEST, null, 'question does not exist');

		// list ids
		$fromCategorySequence = (array) explode(',', ltrim($fromCategorySequence, ','));
		$toCategorySequence = (array) explode(',', ltrim($toCategorySequence, ','));

		// is the question moved to a new category?
		if($fromCategoryId != $toCategoryId)
		{
			$item['id'] = $questionId;
			$item['category_id'] = $toCategoryId;

			BackendFaqModel::update($item);

			// loop id's and set new sequence
			foreach($toCategorySequence as $i => $id)
			{
				$item = array();
				$item['id'] = (int) $id;
				$item['sequence'] = $i + 1;

				// update sequence if the item exists
				if(BackendFaqModel::exists($item['id'])) BackendFaqModel::update($item);
			}
		}

		// loop id's and set new sequence
		foreach($fromCategorySequence as $i => $id)
		{
			$item['id'] = (int) $id;
			$item['sequence'] = $i + 1;

			// update sequence if the item exists
			if(BackendFaqModel::exists($item['id'])) BackendFaqModel::update($item);
		}

		// success output
		$this->output(self::OK, null, 'sequence updated');
	}
}
