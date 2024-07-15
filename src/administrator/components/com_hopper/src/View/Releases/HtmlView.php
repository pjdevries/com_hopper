<?php
/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Hopper\Administrator\View\Releases;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Obix\Component\Hopper\Administrator\Extension\PackageHelper;
use Obix\Component\Hopper\Administrator\Model\ReleasesModel;

class HtmlView extends BaseHtmlView
{
    public $filterForm;

    public $state;
    public $items = [];
    public $pagination;
    public $activeFilters = [];

    public function display($tpl = null): void
    {
        $user = Factory::getApplication()->getIdentity();

        if (!$user->authorise('core.manage', 'com_hopper')) {
            throw new GenericDataException('Not allowed', 403);
        }

        /** @var ReleasesModel $model */
        $model = $this->getModel();

        $this->state = $model->getState();
        $this->items = $model->getItems();
        $this->pagination = $model->getPagination();;
        $this->filterForm = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();

        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode('\n', $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $user = Factory::getApplication()->getIdentity();

        // Get the toolbar object instance
        $toolbar = Toolbar::getInstance('toolbar');

        ToolbarHelper::title(
            Text::_('COM_HOPPER_RELEASES_MANAGE_TITLE'),
            'diagram-release'
        );

        $user = Factory::getApplication()->getIdentity();

        if ($user->authorise('release.create', 'com_hopper')){
            $toolbar->addNew('release.add');
        }

        if ($user->authorise('core.admin', 'com_hopper')
            || $user->authorise(
                'core.options',
                'com_hopper'
            )) {
            $toolbar->preferences('com_hopper');
        }

        ToolbarHelper::divider();
//        ToolbarHelper::help('', false, 'http://joomla.org');

        HTMLHelper::_('sidebar.setAction', 'index.php?option=com_hopper');
    }
}