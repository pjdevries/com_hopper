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
use Joomla\CMS\MVC\Model\ListModelInterface;
use Obix\Component\Handover\Administrator\Extension\Handover\ExportFile;
use Obix\Component\Handover\Administrator\Extension\HandoverComponent;

class ExportModel extends BaseModel
{
    public function export(): void
    {
        $outputDir = $this->getState('outputDir', JPATH_SITE . '/tmp');

        // Categories
        /** @var \Obix\Component\Handover\Administrator\Model\CategoriesModel $categoriesModel */
        $categoriesModel = HandoverComponent::getContainer()
            ->get(MVCFactoryInterface::class)->createModel('Categories', 'Administrator', ['ignore_request' => true]);
        $categoriesModel->setState('list.select', 'a.*');
        $this->exportType('categories', $categoriesModel, 'categories.json', $outputDir);

        // Field categories
        /** @var \Obix\Component\Handover\Administrator\Model\FieldsCategoriesModel $fieldGroupsModel */
        $fieldsCategoriesModel = HandoverComponent::getContainer()
            ->get(MVCFactoryInterface::class)->createModel('FieldsCategories', 'Administrator', ['ignore_request' => true]);
        $this->exportType('fields_categories', $fieldsCategoriesModel, 'fields_categories.json', $outputDir);

        // Field groups
        /** @var \Joomla\Component\Fields\Administrator\Model\GroupsModel $fieldGroupsModel */
        $fieldGroupsModel = Factory::getApplication()->bootComponent('com_fields')
            ->getMVCFactory()->createModel('Groups', 'Administrator', ['ignore_request' => true]);
        $fieldGroupsModel->setState('filter.context', '');
        $fieldGroupsModel->setState('list.select', 'a.*');
        $this->exportType('fields_groups', $fieldGroupsModel, 'fields_groups.json', $outputDir);

        // Fields
        /** @var \Joomla\Component\Fields\Administrator\Model\FieldsModel $fieldsModel */
        $fieldsModel = Factory::getApplication()->bootComponent('com_fields')
            ->getMVCFactory()->createModel('Fields', 'Administrator', ['ignore_request' => true]);
        $fieldsModel->setState('list.select', 'a.*');
        $this->exportType('fields', $fieldsModel, 'fields.json', $outputDir);
    }

    private function exportType(string $type, ListModelInterface $model, string $outputFile, $outputDir): void
    {
        $handoverFile = new ExportFile($outputFile, $outputDir);

        try {
            $handoverFile->export($type, $model);
        } catch (\Exception $e) {
            $handoverFile->delete();

            throw $e;
        }
    }
}