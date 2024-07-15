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
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\FormModel;
use Obix\Component\Hopper\Administrator\Extension\Hopper\HopperType;
use Obix\Component\Hopper\Administrator\Extension\Hopper\Importer\Categories;
use Obix\Component\Hopper\Administrator\Extension\Hopper\Importer\FieldGroups;
use Obix\Component\Hopper\Administrator\Extension\Hopper\Importer\Fields;
use Obix\Component\Hopper\Administrator\Extension\Hopper\Importer\ImporterFactory;
use Obix\Component\Hopper\Administrator\Extension\Upload\Handler;

use RuntimeException;

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

        $verifiedFiles = $this->verifyUploadedFiles($uploadedFiles);

        $this->importFiles($verifiedFiles);
    }

    public function importFiles(array $files, ...$args): void
    {
        //        $db = $this->getDatabase();

        try {
//            $db->transactionStart();

            $categoryIdMap = [];
            $fieldGroupIdMap = [];
            $fieldCategories = [];

            if (isset($files[HopperType::FieldCategories->value])) {
                $data = $this->importData($files[HopperType::FieldCategories->value]);

                $fieldCategories = array_reduce($data, function (array $carry, $item) {
                    $carry[$item->field_id][] = $item->category_id;

                    return $carry;
                }, []);
            }

            if (isset($files[HopperType::Categories->value])) {
                /** @var Categories $categoriesImporter */
                $categoriesImporter = ImporterFactory::createImporter(HopperType::Categories);
                $categoriesImporter->import($this->importData($files[HopperType::Categories->value]));
                $categoryIdMap = $categoriesImporter->getIdMap();
            }

            if (isset($files[HopperType::FieldGroups->value])) {
                /** @var FieldGroups $fieldGroupsImporter */
                $fieldGroupsImporter = ImporterFactory::createImporter(HopperType::FieldGroups);
                $fieldGroupsImporter->import($this->importData($files[HopperType::FieldGroups->value]));
                $fieldGroupIdMap = $fieldGroupsImporter->getIdMap();
            }

            if (isset($files[HopperType::Fields->value])) {
                /** @var Fields $fieldsImporter */
                $fieldsImporter = ImporterFactory::createImporter(HopperType::Fields)
                    ->setGroupIdMap($fieldGroupIdMap)
                    ->setCategoryIdMap($categoryIdMap)
                    ->setFieldCategories($fieldCategories);
                $fieldsImporter->import($this->importData($files[HopperType::Fields->value]));
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
            $hopperObject = json_decode(
                file_get_contents($uploadedFile['dest_path']),
                false,
                512,
                JSON_THROW_ON_ERROR
            );

            if (($type = $hopperObject?->type ?? null) === null || HopperType::tryFrom($type) === null) {
                throw new RuntimeException(
                    Text::sprintf('COM_HOPPER_IMPORT_UPLOAD_NOT_AN_IMPORT_FILE', $uploadedFile['name'])
                );
            }

            if (isset($filesByType[$type])) {
                throw new RuntimeException(
                    Text::sprintf('COM_HOPPER_IMPORT_UPLOAD_DUPLICATE_TYPE', $uploadedFile['name'])
                );
            }

            $filesByType[$type] = $uploadedFile['dest_path'];
        }

        return $filesByType;
    }

    public function getForm($data = [], $loadData = true): Form|null
    {
        if (!($form = $this->loadForm('com_hopper.import', 'import', ['control' => 'jform', 'load_data' => $loadData]
        ))) {
            return null;
        }

        return $form;
    }
}