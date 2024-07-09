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
use Joomla\CMS\Router\Route;
use Obix\Component\Hopper\Administrator\Model\ImportModel;

use function defined;

class ImportController extends BaseController
{
    public function import(): void
    {
        /** @var ImportModel $model */
        $model = $this->getModel();
        $model->setState('importFiles', $this->app->getInput()->files->get('jform', [], 'RAW'));
        $model->import();

        $this->goHome();
    }

    public function cancel(): void
    {
        $this->goHome();
    }

    private function goHome(): void
    {
        $this->setRedirect(Route::_('index.php?option=com_hopper', false));
    }
}