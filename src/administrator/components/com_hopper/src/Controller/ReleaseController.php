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

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Obix\Component\Hopper\Administrator\Extension\Pathname;

class ReleaseController extends FormController
{
    public function download()
    {
        if (!$version = $this->app->getInput()->getString('version', '')) {
            return;
        }

        $packageFile = (new Pathname())->packagesFolder() . '/pkg_hopper-' . $version . '.zip';

        if (!file_exists($packageFile)) {
            return;
        }

        header('Content-type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($packageFile) . '"');
        header('Content-Length: ' . filesize($packageFile));

        readfile($packageFile);

        $this->app->close();
    }

    protected function allowAdd($data = [])
    {
        $user = $this->app->getIdentity();

        return $user->authorise('core.create', $this->option);
    }
}