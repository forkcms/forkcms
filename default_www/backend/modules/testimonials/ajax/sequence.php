<?php

/**
 * Reorder testimonials
 *
 * @package		backend
 * @subpackage	testimonials
 *
 * @author		Jan Moesen <jan@netlash.com>
 * @since		2.1
 */
class BackendTestimonialsAjaxSequence extends BackendBaseAJAXAction
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
		$newIdSequence = trim(SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

		// list id
		$ids = (array) explode(',', rtrim($newIdSequence, ','));

		// loop id's and set new sequence
		foreach($ids as $i => $id)
		{
			// build item
			$item['id'] = (int) $id;
			$item['sequence'] = $i + 1;

			// exists
			if(BackendTestimonialsModel::exists($item['id'])) BackendTestimonialsModel::update($item);
		}

		// success output
		$this->output(self::OK, null, 'sequence updated');
	}
}

?>