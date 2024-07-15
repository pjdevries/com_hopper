<?php
/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Hopper\Administrator\View\Release;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Obix\Component\Hopper\Administrator\Model\ReleaseModel;

use function defined;

class HtmlView extends BaseHtmlView
{
    public $form;
    public $state;
    public $item;

    public function display($tpl = null): void
    {
        /** @var ReleaseModel $model */
        $model = $this->getModel();

        $this->form = $model->getForm();
        $this->state = $model->getState();
        $this->item = $model->getItem();

        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        $canDo = ContentHelper::getActions('com_hopper');

        $toolbar = Toolbar::getInstance();

        ToolbarHelper::title(
            Text::_('COM_HOPPER_RELEASE_MANAGE_TITLE_' . ($isNew ? 'ADD' : 'EDIT'))
        );

        if ($canDo->get('core.create')) {
            if ($isNew) {
                $toolbar->apply('release.save');
            } else {
                $toolbar->apply('release.apply');
            }
            $toolbar->save('release.save');
        }

        $toolbar->cancel('release.cancel', 'JTOOLBAR_CLOSE');
    }
}