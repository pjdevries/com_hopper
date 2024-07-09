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
use Obix\Component\Handover\Administrator\Extension\Package\ComponentPackage;
use Obix\Component\Handover\Administrator\Extension\Package\FilesPackage;
use Obix\Component\Handover\Administrator\Extension\Package\Manifest\FilesManifestAttributes;
use Obix\Component\Handover\Administrator\Extension\Package\Manifest\Manifest;
use Obix\Component\Handover\Administrator\Extension\Package\Package;
use Obix\Component\Handover\Administrator\Extension\Settings;
use Obix\Component\Handover\Administrator\Model\ExportModel;

use function defined;

class ExportController extends BaseController
{
    public function export(): void
    {
        $settings = new Settings();

        /** @var ExportModel $model */
        $model = $this->getModel();

        $version = implode(
            '.',
            $this->app->getInput()->get('jform', ['major' => '1', 'minor' => '0', 'patch' => '0'])['version']
        );

        $model->export($settings);

        (new FilesPackage($settings))->create($version);
        (new ComponentPackage($settings))->create();
        (new Package($settings))->create();

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