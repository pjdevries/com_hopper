<?php
/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Hopper\Administrator\View\Cpanel;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

use function defined;

class HtmlView extends BaseHtmlView
{
    public function display($tpl = null): void
    {
        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
//        Factory::getApplication()->input->set('hidemainmenu', true);

        $canDo = ContentHelper::getActions('com_hopper');

        $toolbar = Toolbar::getInstance();

        ToolbarHelper::title(Text::_('COM_HOPPER'));

        if ($canDo->get('core.options')) {
            $toolbar->preferences('com_hopper');
        }
    }
}