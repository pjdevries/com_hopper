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

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Obix\Component\Handover\Administrator\Extension\Handover\Importer\ImporterFactory;

class ImportModel extends BaseDatabaseModel
{
    public function import(): void
    {
        $inputDir = $this->getState('inputDir');

        $iterator = new \GlobIterator($inputDir . '/*.json');

        $db = $this->getDatabase();

        try {
            $db->transactionStart();

            foreach ($iterator as $file) {
                $handoverObject = json_decode(file_get_contents($file->getPathname()), false, 512, JSON_THROW_ON_ERROR);

                if ($type = $handoverObject?->type ?? null) {
                    $this->importType($type, $handoverObject->data);
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
}