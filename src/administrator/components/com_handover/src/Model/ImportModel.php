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

use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\FormModel;
use Obix\Component\Handover\Administrator\Extension\Handover\Importer\ImporterFactory;
use Obix\Component\Handover\Administrator\Extension\Upload\Handler;

class ImportModel extends FormModel
{
    public function import(): void
    {
        // Get uploaded files from request.
        if (!count($files = $this->getState('importFiles', []))) {
            return;
        }

        $handlers = Handler::handle($files, $this->getForm([], false));

        $db = $this->getDatabase();

        try {
            $db->transactionStart();

            foreach ($handlers as $fieldName => $handler) {
                $uploadedFiles = $handler->getSuccesful();

                if (!count($uploadedFiles)) {
                    continue;
                }

                foreach ($handler->getUploadedFiles() as $uploadedFile) {
                    $handoverObject = json_decode(
                        file_get_contents($uploadedFile['dest_path']),
                        false,
                        512,
                        JSON_THROW_ON_ERROR
                    );

                    if ($type = $handoverObject?->type ?? null) {
                        $this->importType($type, $handoverObject->data);
                    }
                }
            }

            $db->transactionCommit();
        } catch (\Exception $e) {
            $db->transactionRollback();

            throw $e;
        }
    }

    private function importType(string $type, array $data): void
    {
        $importer = ImporterFactory::createImporter($type);
        $importer->import($data);
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