<?php
/**
 * @package     com_handover
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Handover\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Obix\Component\Handover\Administrator\Model\ExportModel;

use function defined;

class ExportController extends BaseController
{
    public function export(): void
    {
        /** @var ExportModel $model */
        $model = $this->getModel();
        $model->setState('outputDir', JPATH_SITE . '/tmp');
        $model->export();

        $this->goHome();
    }

    public function cancel(): void
    {
        $this->goHome();
    }

    private function goHome(): void
    {
        $this->setRedirect(Route::_('index.php?option=com_handover', false));
    }
}