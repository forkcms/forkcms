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
		// no timelimit
		set_time_limit(0);

		// get database
		$db = BackendModel::getDB(true);

		// get process-id
		$pid = getmypid();

		// store PID
		SpoonFile::setContent(BACKEND_CACHE_PATH .'/hooks/pid', $pid);

		// loop forever
		while(true)
		{
			// get 1 item
			$item = $db->getRecord('SELECT *
									FROM hooks_queue
									WHERE status = ?
									LIMIT 1',
									array('queued'));

			// any item?
			if(!empty($item))
			{
				// set item as busy
				$db->update('hooks_queue', array('status' => 'busy'), 'id = ?', array($item['id']));

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
					}
				}
				catch(Exception $e)
				{
					// set to error state
					$db->update('hooks_queue', array('status' => 'error'), 'id = ?', $item['id']);
				}

				// everything went fine so delete the item
				$db->delete('hooks_queue', 'id = ?', $item['id']);
			}

			// stop it
			else
			{
				// remove the file
				SpoonFile::delete(BACKEND_CACHE_PATH .'/hooks/pid');

				// stop the script
				exit;
			}
		}
	}
}

?>