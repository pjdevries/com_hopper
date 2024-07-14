<?php
/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Hopper\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\View\ViewInterface;
use Obix\Component\Hopper\Administrator\Extension\Package\ComponentPackage;
use Obix\Component\Hopper\Administrator\Extension\Package\FilesPackage;
use Obix\Component\Hopper\Administrator\Extension\Package\Manifest\FilesManifestAttributes;
use Obix\Component\Hopper\Administrator\Extension\Package\Manifest\Manifest;
use Obix\Component\Hopper\Administrator\Extension\Package\Manifest\PackageManifestAttributes;
use Obix\Component\Hopper\Administrator\Extension\Package\Package;
use Obix\Component\Hopper\Administrator\Extension\Pathname;

class ReleaseModel extends AdminModel
{
    public function save($data): bool
    {
        if (!(int) $data['id']) {
            $data['created_by'] = Factory::getApplication()->getIdentity()->id;
        }

        $data['modified_by'] = Factory::getApplication()->getIdentity()->id;

        if (!($result = parent::save($data))) {
            return $result;
        }

        $releaseId = (int)$this->getState($this->name . '.id');
        $item = $this->getItem($releaseId);

        $this->export($item->project_id, $item->version);

        return true;
    }

    private function export(string $projectId, string $version): void
    {
        $settings = new Pathname();

        /** @var ProjectModel $exportModel */
        $projectModel = $this->getMVCFactory()->createModel(
            'Project',
            'Administrator',
            ['ignore_request' => true]
        );
        $project = $projectModel->getItem($projectId);

        /** @var ExportModel $model */
        $exportModel = $this->getMVCFactory()->createModel(
            'Export',
            'Administrator',
            ['ignore_request' => true]
        );

        $exportModel->setState('version', $version);
        $exportModel->export($settings, $project->alias, $version);

        (new FilesPackage($settings, new Manifest(new FilesManifestAttributes($settings, $version))))->create($project->alias, $version);
        (new ComponentPackage($settings))->create();
        (new Package($settings, new Manifest(new PackageManifestAttributes($settings, $version))))->create($project->alias);
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_hopper.release', 'release', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_hopper.edit.release.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    public function getTable($name = '', $prefix = '', $options = array())
    {
        $name = 'Releases';
        $prefix = 'Table';

        if ($table = $this->_createTable($name, $prefix, $options)) {
            return $table;
        }

        throw new \Exception(Text::sprintf('JLIB_APPLICATION_ERROR_TABLE_NAME_NOT_SUPPORTED', $name), 0);
    }
}