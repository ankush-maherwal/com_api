<?php
/**
 * @package    Com.Api
 *
 * @copyright  Copyright (C) 2005 - 2017 Techjoomla, Techjoomla Pvt. Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

/**
 * View class for list of logs
 *
 * @since  1.0
 */
class ApiViewLogs extends JViewLegacy
{
	/**
	 * The model state.
	 *
	 * @var   JObject
	 * @since 1.0
	 */
	protected $state;

	/**
	 * The item data.
	 *
	 * @var   object
	 * @since 1.0
	 */
	protected $items;

	/**
	 * The pagination object.
	 *
	 * @var   JPagination
	 * @since 1.0
	 */
	protected $pagination;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		ApiHelper::addSubmenu('logs');

		$this->addToolbar();

		if (JVERSION >= '3.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/api.php';

		$state = $this->get('State');
		$canDo = ApiHelper::getActions($state->get('filter.category_id'));

		if (JVERSION >= '3.0')
		{
			JToolBarHelper::title(JText::_('COM_API_TITLE_LOGS'), 'list');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_API_TITLE_LOGS'), 'logs.png');
		}

		if ($canDo->get('core.edit.state'))
		{
			// If this component does not use state then show a direct delete button as we can not trash
			JToolBarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'logs.delete', 'JTOOLBAR_DELETE');
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == - 2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'logs.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('logs.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_api');
		}

		if (JVERSION >= '3.0')
		{
			// Set sidebar action - New in 3.0
			JHtmlSidebar::setAction('index.php?option=com_api&view=logs');
		}

		$this->extra_sidebar = '';
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'u.name' => JText::_('COM_API_LOGS_USER'),
			'a.hash' => JText::_('COM_API_KEYS_HASH'),
			'a.ip_address' => JText::_('COM_API_LOGS_IP_ADDRESS'),
			'a.time' => JText::_('COM_API_LOGS_TIME'),
			'a.request_method' => JText::_('COM_API_LOGS_REQUEST_METHOD')
		);
	}
}
