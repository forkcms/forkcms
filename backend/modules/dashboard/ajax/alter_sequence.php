<?php

/**
 * This will alter the sequence of the widgets
 *
 * @package		backend
 * @subpackage	dashboard
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendDashboardAjaxAlterSequence extends BackendBaseAJAXAction
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
		$newSequence = SpoonFilter::getPostValue('new_sequence', null, '');

		// validate
		if($newSequence == '') $this->output(self::BAD_REQUEST, null, 'no new_sequence provided');

		// convert into array
		$json = @json_decode($newSequence, true);

		// validate
		if($json === false) $this->output(self::BAD_REQUEST, null, 'invalid new_sequence provided');

		// initialize
		$userSequence = array();
		$hiddenItems = array();

		// loop columns
		foreach($json as $column => $widgets)
		{
			$columnValue = 'left';
			if($column == 1) $columnValue = 'middle';
			if($column == 2) $columnValue = 'right';

			// loop widgets
			foreach($widgets as $sequence => $widget)
			{
				// store position
				$userSequence[$widget['module']][$widget['widget']] = array('column' => $columnValue, 'position' => $sequence, 'hidden' => $widget['hidden'], 'present' => $widget['present']);

				// add to array
				if($widget['hidden']) $hiddenItems[] = $widget['module'] . '_' . $widget['widget'];
			}
		}

		// get previous setting
		$currentSetting = BackendAuthentication::getUser()->getSetting('dashboard_sequence');
		$data['reload'] = false;

		// any settings?
		if($currentSetting !== null)
		{
			// loop modules
			foreach($currentSetting as $module => $widgets)
			{
				foreach($widgets as $widget => $values)
				{
					if($values['hidden'] && isset($userSequence[$module][$widget]['hidden']) && !$userSequence[$module][$widget]['hidden'])
					{
						$data['reload'] = true;
					}
				}
			}
		}

		// store
		BackendAuthentication::getUser()->setSetting('dashboard_sequence', $userSequence);

		// output
		$this->output(self::OK, $data, BL::msg('Saved'));
	}
}

?>