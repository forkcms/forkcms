<?php

/**
 * Delete a testimonial.
 *
 * @package		backend
 * @subpackage	testimonials
 *
 * @author		Jan Moesen <jan@netlash.com>
 * @since		2.1
 */
class BackendTestimonialsDelete extends BackendBaseActionDelete
{
	/**
	 * Execute the current action.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendTestimonialsModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get the current testimonial
			$this->record = BackendTestimonialsModel::get($this->id);

			// delete it
			BackendTestimonialsModel::delete($this->id);

			// redirect back to the index
			$this->redirect(BackendModel::createURLForAction('index') .'&report=deleted&var='. urlencode($this->record['name']));
		}

		// no testimonial found
		else $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');
	}
}

?>