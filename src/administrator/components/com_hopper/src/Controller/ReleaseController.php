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

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\View\ViewInterface;
use Obix\Component\Hopper\Administrator\Extension\PackageHelper;
use Obix\Component\Hopper\Administrator\Model\ExportModel;
use Obix\Component\Hopper\Administrator\Model\ProjectModel;

use function defined;

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

        /** @var ExportModel $model */
        $exportModel = $this->createModel(
            'Export',
            'Administrator',
            ['ignore_request' => true]
        );

        $exportModel->export(new PackageHelper($project->alias, $version));
    }

    public function download()
    {
        if (!$projectId = $this->app->getInput()->getInt('projectId', 0)) {
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