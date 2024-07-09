<?php
/**
 * @package     com_handover
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Handover\Administrator\View\Export;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

use function defined;

class HtmlView extends BaseHtmlView
{
    protected ?Form $form = null;
    public function display($tpl = null): void
    {
        $this->form = $this->getForm();

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);

        $canDo = ContentHelper::getActions('com_handover');

        $toolbar = Toolbar::getInstance();

        ToolbarHelper::title(Text::_('COM_HANDOVER_EXPORT_TITLE'), 'export');

        if ($canDo->get('core.export')) {
            $toolbar->standardButton('export', 'COM_HANDOVER_ACTION_EXPORT', 'export.export');
        }

        $toolbar->cancel('export.cancel', 'JTOOLBAR_CLOSE');
    }
}