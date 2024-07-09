<?php
/**
 * @package     com_handover
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Handover\Administrator\Extension\Handover;

defined('_JEXEC') or die;

use RuntimeException;

use function defined;

class ExportFile
{
    private string $outputDir;

    private string $outputPath;

    /**
     * @param string $outputPath
     */
    public function __construct(string $ouputFile, string $outputDir = JPATH_SITE . '/tmp')
    {
        $this->outputDir = rtrim($outputDir, '\\/');
        $this->outputPath = $this->outputDir . '/' . $ouputFile;
    }

    public function export(HandoverType $type, array $items): void
    {
        $this->mkDir();
        $this->delete();

        if (file_put_contents($this->outputPath, json_encode([
                'type' => $type->value,
                'data' => $items
            ], JSON_PRETTY_PRINT)) === false) {
            $error = error_get_last();
            throw new RuntimeException($error['message'], $error['type']);
        }
    }

    private function mkDir(): void
    {
        if (!file_exists($this->outputDir) && !mkdir($this->outputDir, 0755, true)) {
            $error = error_get_last();
            throw new RuntimeException($error['message'], $error['type']);
        }
    }

    public function delete(): void
    {
        if (!file_exists($this->outputPath)) {
            return;
        }

        if (!unlink($this->outputPath)) {
            $error = error_get_last();
            throw new RuntimeException($error['message'], $error['type']);
        }
    }
}