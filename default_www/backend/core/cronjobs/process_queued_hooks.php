<?php

/**
 * This is the cronjob to processes the queued hooks.
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.2
 */
class BackendCoreCronjobProcessQueuedHooks extends BackendBaseCronjob
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// ignore user abortion, so the script will keep running
		ignore_user_abort(true);

		// no timelimit
		set_time_limit(0);

		// get database
		$db = BackendModel::getDB(true);

		$id = SpoonFilter::getGetValue('id', null, '');


		// any items
		if($id != '')
		{
			// make them busy
			$db->update('hooks_queue', array('status' => 'busy'), 'id = ?', array($id));

			$item = $db->getRecord('SELECT *
									FROM hooks_queue
									WHERE id = ?',
									array($id));

			// unserialize data
			$item['callback'] = unserialize($item['callback']);
			$item['data'] = unserialize($item['data']);

			// check if the item is callable
			if(!is_callable($item['callback']))
			{
				// in debug mode we want to know if there are errors
				if(SPOON_DEBUG) throw new BackendException('Invalid callback.');

				// set to error state
				$db->update('hooks_queue', array('status' => 'error'), 'id = ?', $item['id']);

				// stop
				exit;
			}

			try
			{
				// call the callback
				$return = call_user_func($item['callback'], $item['data']);

				// failed?
				if($return === false)
				{
					// set to error state
					$db->update('hooks_queue', array('status' => 'error'), 'id = ?', $item['id']);

					// stop
					exit;
				}
			}
			catch(Exception $e)
			{
				// in debug mode we want to see the errors
				if(SPOON_DEBUG) throw $e;

				// set to error state
				$db->update('hooks_queue', array('status' => 'error'), 'id = ?', $item['id']);

				// stop
				exit;
			}

			// everything went fine so delete the item
			$db->delete('hooks_queue', 'id = ?', $item['id']);
		}
	}
}

?>