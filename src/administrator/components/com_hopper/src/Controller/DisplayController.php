<?php
/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Hopper\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\View\ViewInterface;

use function defined;

class DisplayController extends BaseController
{
    protected $default_view = 'cpanel';

    protected function prepareViewModel(ViewInterface $view)
    {
        parent::prepareViewModel($view);

        if ($view->getName() === 'releases') {
            $view->setModel($this->getModel('project'));
        }
    }
}