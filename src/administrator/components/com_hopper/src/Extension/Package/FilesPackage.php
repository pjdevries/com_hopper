<?php
/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Hopper\Administrator\Extension\Package;

use Obix\Component\Hopper\Administrator\Extension\Package\Manifest\FilesManifestAttributes;
use Obix\Component\Hopper\Administrator\Extension\Package\Manifest\Manifest;
use Obix\Component\Hopper\Administrator\Extension\Pathname;
use ZipArchive;

\defined('_JEXEC') or die;

class FilesPackage
{
    private Pathname $settings;

    private Manifest $manifest;

    /**
     * @param Pathname $settings
     */
    public function __construct(Pathname $settings, Manifest $manifest)
    {
        $this->settings = $settings;
        $this->manifest = $manifest;
    }

    public function create(string $projectAlias, string $version = '1.0.0'): void
    {
        $this->manifest->generate(
            $this->settings->manifestTemplatesFolder($projectAlias) . '/hopper_import_files_manifest.template.xml',
            $this->settings->exportFolder($projectAlias, $version) . '/hopper_import_files.xml'
        );

        $archive = new ZipArchive();

        $archive->open($this->settings->packagesFolder($projectAlias) . '/hopper_import_files-' . $version . '.zip', ZipArchive::OVERWRITE | ZipArchive::CREATE);

        $archive->addFile($this->settings->exportFolder($projectAlias, $version) . '/hopper_import_files.xml', 'hopper_import_files.xml');
        $archive->addGlob($this->settings->exportFilesFolder($projectAlias, $version) . '/*', 0, ['add_path' => basename($this->settings->exportFilesFolder($projectAlias, $version)) . '/', 'remove_all_path' => TRUE]);

        $archive->close();
    }
}