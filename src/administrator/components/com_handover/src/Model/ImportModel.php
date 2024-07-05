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

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\FormModel;
use Obix\Component\Handover\Administrator\Extension\Handover\HandoverType;
use Obix\Component\Handover\Administrator\Extension\Handover\Importer\Categories;
use Obix\Component\Handover\Administrator\Extension\Handover\Importer\FieldGroups;
use Obix\Component\Handover\Administrator\Extension\Handover\Importer\Fields;
use Obix\Component\Handover\Administrator\Extension\Handover\Importer\ImporterFactory;
use Obix\Component\Handover\Administrator\Extension\Upload\Handler;

use function defined;

class ImportModel extends FormModel
{
    // TODO: enable transactions
    public function import(): void
    {
        // Get uploaded files from request.
        if (!count($files = $this->getState('importFiles', []))) {
            return;
        }

        $handlers = Handler::handle($files, $this->getForm([], false));

        // Only one handler expected.
        $handler = reset($handlers);

        if (!count($uploadedFiles = $handler->getSuccesful())) {
            return;
        }

        $uploadedFiles = $this->verifyUploadedFiles($uploadedFiles);

//        $db = $this->getDatabase();

        try {
//            $db->transactionStart();

            $categoryIdMap = [];
            $fieldGroupIdMap = [];
            $fieldCategories = [];

            if (isset($uploadedFiles[HandoverType::FieldCategories->value])) {
                $data = $this->importData($uploadedFiles[HandoverType::FieldCategories->value]['dest_path']);

                $fieldCategories = array_reduce($data, function (array $carry, $item) {
                    $carry[$item->field_id][] = $item->category_id;

                    return $carry;
                }, []);
            }

            if (isset($uploadedFiles[HandoverType::Categories->value])) {
                /** @var Categories $categoriesImporter */
                $categoriesImporter = ImporterFactory::createImporter(HandoverType::Categories);
                $categoriesImporter->import($this->importData($uploadedFiles[HandoverType::Categories->value]['dest_path']));
                $categoryIdMap = $categoriesImporter->getIdMap();
            }

            if (isset($uploadedFiles[HandoverType::FieldGroups->value])) {
                /** @var FieldGroups $fieldGroupsImporter */
                $fieldGroupsImporter = ImporterFactory::createImporter(HandoverType::FieldGroups);
                $fieldGroupsImporter->import($this->importData($uploadedFiles[HandoverType::FieldGroups->value]['dest_path']));
                $fieldGroupIdMap = $fieldGroupsImporter->getIdMap();
            }

            if (isset($uploadedFiles[HandoverType::Fields->value])) {
                /** @var Fields $fieldsImporter */
                $fieldsImporter = ImporterFactory::createImporter(HandoverType::Fields)
                    ->setGroupIdMap($fieldGroupIdMap)
                    ->setCategoryIdMap($categoryIdMap)
                    ->setFieldCategories($fieldCategories);
                $fieldsImporter->import($this->importData($uploadedFiles[HandoverType::Fields->value]['dest_path']));
            }

//            $db->transactionCommit();
        } catch (Exception $e) {
//            $db->transactionRollback();

            throw $e;
        }
    }

    private function importData(string $fileName): array
    {
        return json_decode(
            file_get_contents($fileName),
            false,
            512,
            JSON_THROW_ON_ERROR
        )->data;
    }

    private function verifyUploadedFiles(array $uploadedFiles): array
    {
        $filesByType = [];

        foreach ($uploadedFiles as $uploadedFile) {
            $handoverObject = json_decode(
                file_get_contents($uploadedFile['dest_path']),
                false,
                512,
                JSON_THROW_ON_ERROR
            );

            if (($type = $handoverObject?->type ?? null) === null || HandoverType::tryFrom($type) === null) {
                throw new \RuntimeException(Text::sprintf('COM_HANDOVER_IMPORT_UPLOAD_NOT_AN_IMPORT_FILE', $uploadedFile['name']));
            }

            if (isset($filesByType[$type])) {
                throw new \RuntimeException(Text::sprintf('COM_HANDOVER_IMPORT_UPLOAD_DUPLICATE_TYPE', $uploadedFile['name']));
            }

            $filesByType[$type] = $uploadedFile;
        }

        return $filesByType;
    }

    public function getForm($data = [], $loadData = true): Form|null
    {
        if (!($form = $this->loadForm('com_handover.import', 'import', ['control' => 'jform', 'load_data' => $loadData]
        ))) {
            return null;
        }

        return $form;
    }
}