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

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\Component\Fields\Administrator\Model\FieldsModel;
use Joomla\Component\Fields\Administrator\Model\GroupsModel;
use Obix\Component\Hopper\Administrator\Extension\Hopper\ExportFile;
use Obix\Component\Hopper\Administrator\Extension\Hopper\HopperType;
use Obix\Component\Hopper\Administrator\Extension\Package\ComponentPackage;
use Obix\Component\Hopper\Administrator\Extension\Package\FilesPackage;
use Obix\Component\Hopper\Administrator\Extension\Package\Manifest\FilesManifestAttributes;
use Obix\Component\Hopper\Administrator\Extension\Package\Manifest\Manifest;
use Obix\Component\Hopper\Administrator\Extension\Package\Manifest\PackageManifestAttributes;
use Obix\Component\Hopper\Administrator\Extension\Package\Package;
use Obix\Component\Hopper\Administrator\Extension\PackageHelper;

use function defined;

class ExportModel extends FormModel
{
    private PackageHelper $packageHelper;

    public function export(PackageHelper $packageHelper): void
    {
        $this->packageHelper = $packageHelper;

        $this->createImportFiles();

        (new FilesPackage($packageHelper, new Manifest(new FilesManifestAttributes($packageHelper))))->create();
        (new ComponentPackage($packageHelper))->create();
        (new Package($packageHelper, new Manifest(new PackageManifestAttributes($packageHelper))))->create();
    }

    private function createImportFiles(): void
    {
        $outputDir = $this->packageHelper->exportFilesFolder();

        // Field categories
        /** @var FieldsCategoriesModel $fieldGroupsModel */
        $fieldsCategoriesModel = $this->getMVCFactory()->createModel(
            'FieldsCategories',
            'Administrator',
            ['ignore_request' => true]
        );
        $this->createImportFileForType(
            HopperType::FieldCategories,
            $fieldCategories = $fieldsCategoriesModel->getItems(),
            HopperType::FieldCategories->toFileName(),
            $outputDir
        );

        // Categories
        /** @var CategoriesModel $categoriesModel */
        $categoriesModel = $this->getMVCFactory()->createModel('Categories', 'Administrator', ['ignore_request' => true]
        );
        $categoriesModel->setState('list.select', 'a.*');
        $categoriesModel->setState('filter.id', array_map(fn(object $item) => $item->category_id, $fieldCategories));
        $this->createImportFileForType(
            HopperType::Categories,
            $categoriesModel->getItems(true),
            HopperType::Categories->toFileName(),
            $outputDir
        );

        // Field groups
        /** @var GroupsModel $fieldGroupsModel */
        $fieldGroupsModel = Factory::getApplication()->bootComponent('com_fields')
            ->getMVCFactory()->createModel('Groups', 'Administrator', ['ignore_request' => true]);
        $fieldGroupsModel->setState('filter.context', '');
        $fieldGroupsModel->setState('list.select', 'a.*');
        $this->createImportFileForType(
            HopperType::FieldGroups,
            $fieldGroupsModel->getItems(),
            HopperType::FieldGroups->toFileName(),
            $outputDir
        );

        // Fields
        /** @var FieldsModel $fieldsModel */
        $fieldsModel = Factory::getApplication()->bootComponent('com_fields')
            ->getMVCFactory()->createModel('Fields', 'Administrator', ['ignore_request' => true]);
        $fieldsModel->setState('list.select', 'a.*');
        $this->createImportFileForType(
            HopperType::Fields,
            $fieldsModel->getItems(),
            HopperType::Fields->toFileName(),
            $outputDir
        );
    }

    private function createImportFileForType(HopperType $type, array $items, string $outputFile, $outputDir): void
    {
        $hopperFile = new ExportFile($outputFile, $outputDir);

        try {
            $hopperFile->export($type, $items);
        } catch (Exception $e) {
            $hopperFile->delete();

            throw $e;
        }
    }

    public function getForm($data = [], $loadData = true): Form|null
    {
        if (!($form = $this->loadForm('com_hopper.export', 'export', ['control' => 'jform', 'load_data' => $loadData]
        ))) {
            return null;
        }

        return $form;
    }
}