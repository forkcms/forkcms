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
		$db = BackendModel::getDB(true);

		// get all queued hooks
		$items = (array) $db->getRecords('SELECT i.*
											FROM hooks_queue AS i
											WHERE i.status = ?
											LIMIT 5',
											array('queued'), 'id');

		if(!empty($items))
		{
			// make them busy
			$db->update('hooks_queue', array('status' => 'busy'), 'id IN('. implode(',', array_keys($items)) .')');

			// loop items
			foreach($items as &$item)
			{
				// unserialize data
				$item['callback'] = unserialize($item['callback']);
				$item['data'] = unserialize($item['data']);

				// check if the item is callable
				if(!is_callable($item['callback']))
				{
					// set to error state
					$db->update('hooks_queue', array('status' => 'error'), 'id = ?', $item['id']);

					// skip to next item
					continue;
				}

				try
				{
					// call the callback
					call_user_func($item['callback'], $item['data']);
				}
				catch(Exception $e)
				{
					// in debug mode we want to see the errors
					if(SPOON_DEBUG) throw $e;

					// set to error state
					$db->update('hooks_queue', array('status' => 'error'), 'id = ?', $item['id']);
				}

				// everything went fine so delete the item
				$db->delete('hooks_queue', 'id = ?', $item['id']);
			}

			// check if there are still items to process
			if((int) $db->getVar('SELECT COUNT(i.id)
									FROM hooks_queue AS i
									WHERE i.status = ?',
									array('queued')) > 0)
			{
				// @todo	redirect to the cronjob itself, so we can process other items
			}
		}
	}
}

?>