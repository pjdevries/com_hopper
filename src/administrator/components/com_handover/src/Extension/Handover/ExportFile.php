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

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModelInterface;

class ExportFile
{
    private string $outputPath;

    /**
     * @param string $outputPath
     */
    public function __construct(string $ouputFile, string $outputDir = JPATH_SITE . '/tmp')
    {
        $this->outputPath = rtrim($outputDir, '\\/') . '/' . $ouputFile;
    }

    public function export(string $type, ListModelInterface $model,): void
    {
        $this->delete();

        if (file_put_contents($this->outputPath, json_encode([
                'type' => $type,
                'data' => $model->getItems()
            ], JSON_PRETTY_PRINT)) === false) {
            $error = error_get_last();
            throw new \RuntimeException($error['message'], $error['type']);
        }
    }

    public function delete(): bool
    {
        if (!file_exists($this->outputPath)) {
            return true;
        }

        if (!unlink($this->outputPath)) {
            $error = error_get_last();
            throw new \RuntimeException($error['message'], $error['type']);
        }

        return true;
    }
}