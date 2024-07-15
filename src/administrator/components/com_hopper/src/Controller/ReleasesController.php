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

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\View\ViewInterface;

\defined('_JEXEC') or die;

class ReleasesController extends BaseController
{
    protected function prepareViewModel(ViewInterface $view)
    {
        parent::prepareViewModel($view);

        $view->setModel(
            $this->createModel(
                'Project',
                'Administrator',
                ['ignore_request' => true]
            )
        );
    }
}