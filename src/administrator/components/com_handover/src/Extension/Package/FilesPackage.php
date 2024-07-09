<?php
/**
 * @package     com_handover
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Handover\Administrator\Extension\Package;

use Obix\Component\Handover\Administrator\Extension\Package\Manifest\FilesManifestAttributes;
use Obix\Component\Handover\Administrator\Extension\Package\Manifest\Manifest;
use Obix\Component\Handover\Administrator\Extension\Settings;
use ZipArchive;

\defined('_JEXEC') or die;

class FilesPackage
{
    private Settings $settings;

    /**
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function create(string $version = '1.0.0'): void
    {
        (new Manifest(new FilesManifestAttributes($version)))->generate(
            $this->settings->manifestTemplatesFolder() . '/handover_import_files_manifest.template.xml',
            $this->settings->exportFolder() . '/handover_import_files.xml'
        );

        $archive = new ZipArchive();

        $archive->open($this->settings->packagesFolder() . '/handover_import_files-' . $version . '.zip', ZipArchive::OVERWRITE | ZipArchive::CREATE);

        $archive->addFile($this->settings->exportFolder() . '/handover_import_files.xml', 'handover_import_files.xml');
        $archive->addGlob($this->settings->exportFilesFolder() . '/*', 0, ['add_path' => 'files/', 'remove_all_path' => TRUE]);

        $archive->close();
    }
}