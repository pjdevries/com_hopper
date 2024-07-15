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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\View\ViewInterface;
use Obix\Component\Hopper\Administrator\Extension\Package\ComponentPackage;
use Obix\Component\Hopper\Administrator\Extension\Package\FilesPackage;
use Obix\Component\Hopper\Administrator\Extension\Package\Manifest\FilesManifestAttributes;
use Obix\Component\Hopper\Administrator\Extension\Package\Manifest\Manifest;
use Obix\Component\Hopper\Administrator\Extension\Package\Manifest\PackageManifestAttributes;
use Obix\Component\Hopper\Administrator\Extension\Package\Package;
use Obix\Component\Hopper\Administrator\Extension\PackageHelper;
use Obix\Component\Hopper\Administrator\Model\ExportModel;
use Obix\Component\Hopper\Administrator\Model\ProjectModel;

class ReleaseController extends FormController
{
    protected function postSaveHook(BaseDatabaseModel $model, $validData = [])
    {
        parent::postSaveHook($model, $validData);

        $releaseId = (int)$model->getState($model->getName() . '.id');
        $release = $model->getItem($releaseId);

        $this->export($release->project_id, $release->version);
    }


    private function export(string $projectId, string $version): void
    {
        /** @var ProjectModel $projectModel */
        $projectModel = $this->createModel(
            'Project',
            'Administrator',
            ['ignore_request' => true]
        );
        $project = $projectModel->getItem($projectId);

        $packageHelper = new PackageHelper($project->alias, $version);

        /** @var ExportModel $model */
        $exportModel = $this->createModel(
            'Export',
            'Administrator',
            ['ignore_request' => true]
        );

        $exportModel->setState('version', $version);
        $exportModel->export($packageHelper);

        (new FilesPackage($packageHelper, new Manifest(new FilesManifestAttributes($packageHelper, $version))))->create($project->alias, $version);
        (new ComponentPackage($packageHelper))->create();
        (new Package($packageHelper, new Manifest(new PackageManifestAttributes($packageHelper, $version))))->create($project->alias);
    }

    public function download()
    {
        if (!$projectId = $this->app->getInput()->getInt('version', 0)) {
            return;
        }

        if (!$version = $this->app->getInput()->getString('version', '')) {
            return;
        }

        /** @var ProjectModel $exportModel */
        $projectModel = $this->createModel(
            'Project',
            'Administrator',
            ['ignore_request' => true]
        );
        $project = $projectModel->getItem($projectId);

        $packageHelper = new PackageHelper($project->alias, $version);
        $packageFile = $packageHelper->packagesFolder() . '/' . $packageHelper->packageName();

        if (!file_exists($packageFile)) {
            return;
        }

        header('Content-type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($packageFile) . '"');
        header('Content-Length: ' . filesize($packageFile));

        readfile($packageFile);

        $this->app->close();
    }

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

    protected function allowAdd($data = [])
    {
        $user = $this->app->getIdentity();

        return $user->authorise('core.create', $this->option);
    }
}