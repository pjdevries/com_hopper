<?php
/**
 * @package     com_handover
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Handover\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\BaseModel;
use Obix\Component\Handover\Administrator\Extension\Handover\ExportFile;
use Obix\Component\Handover\Administrator\Extension\Handover\HandoverType;
use Obix\Component\Handover\Administrator\Extension\HandoverComponent;

class ExportModel extends BaseModel
{
    public function export(): void
    {
        $outputDir = $this->getState('outputDir', JPATH_SITE . '/tmp');

        // Field categories
        /** @var \Obix\Component\Handover\Administrator\Model\FieldsCategoriesModel $fieldGroupsModel */
        $fieldsCategoriesModel = HandoverComponent::getContainer()
            ->get(MVCFactoryInterface::class)->createModel('FieldsCategories', 'Administrator', ['ignore_request' => true]);
        $this->exportType(HandoverType::FieldCategories, $fieldCategories = $fieldsCategoriesModel->getItems(), 'fields_categories.json', $outputDir);

        // Categories
        /** @var \Obix\Component\Handover\Administrator\Model\CategoriesModel $categoriesModel */
        $categoriesModel = HandoverComponent::getContainer()
            ->get(MVCFactoryInterface::class)->createModel('Categories', 'Administrator', ['ignore_request' => true]);
        $categoriesModel->setState('list.select', 'a.*');
        $categoriesModel->setState('filter.id', array_map(fn(object $item) => $item->category_id, $fieldCategories));
        $this->exportType(HandoverType::Categories, $categoriesModel->getItems(), 'categories.json', $outputDir);

        // Field groups
        /** @var \Joomla\Component\Fields\Administrator\Model\GroupsModel $fieldGroupsModel */
        $fieldGroupsModel = Factory::getApplication()->bootComponent('com_fields')
            ->getMVCFactory()->createModel('Groups', 'Administrator', ['ignore_request' => true]);
        $fieldGroupsModel->setState('filter.context', '');
        $fieldGroupsModel->setState('list.select', 'a.*');
        $this->exportType(HandoverType::FieldGroups, $fieldGroupsModel->getItems(), 'fields_groups.json', $outputDir);

        // Fields
        /** @var \Joomla\Component\Fields\Administrator\Model\FieldsModel $fieldsModel */
        $fieldsModel = Factory::getApplication()->bootComponent('com_fields')
            ->getMVCFactory()->createModel('Fields', 'Administrator', ['ignore_request' => true]);
        $fieldsModel->setState('list.select', 'a.*');
        $this->exportType(HandoverType::Fields, $fieldsModel->getItems(), 'fields.json', $outputDir);
    }

    private function exportType(HandoverType $type, array $items, string $outputFile, $outputDir): void
    {
        $handoverFile = new ExportFile($outputFile, $outputDir);

        try {
            $handoverFile->export($type, $items);
        } catch (\Exception $e) {
            $handoverFile->delete();

            throw $e;
        }
    }
}