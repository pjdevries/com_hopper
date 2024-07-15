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

use Obix\Component\Hopper\Administrator\Extension\Package\Manifest\Manifest;
use Obix\Component\Hopper\Administrator\Extension\PackageHelper;
use ZipArchive;

\defined('_JEXEC') or die;

class FilesPackage
{
    private PackageHelper $packageHelper;

    private Manifest $manifest;

    /**
     * @param PackageHelper $packageHelper
     */
    public function __construct(PackageHelper $packageHelper, Manifest $manifest)
    {
        $this->packageHelper = $packageHelper;
        $this->manifest = $manifest;
    }

    public function create(): void
    {
        $this->manifest->generate(
            $this->packageHelper->manifestTemplatesFolder() . '/hopper_import_files_manifest.template.xml',
            $this->packageHelper->exportFolder() . '/' . $this->packageHelper->filesManifestName()
        );

        $archive = new ZipArchive();

        $archive->open(
            $this->packageHelper->packagesFolder() . '/' . $this->packageHelper->filesPackageName(),
            ZipArchive::OVERWRITE | ZipArchive::CREATE
        );

        $archive->addFile(
            $this->packageHelper->exportFolder() . '/' . $this->packageHelper->filesManifestName(),
            $this->packageHelper->filesManifestName()
        );
        $archive->addGlob(
            $this->packageHelper->exportFilesFolder() . '/*',
            0,
            ['add_path' => basename($this->packageHelper->exportFilesFolder()) . '/', 'remove_all_path' => true]
        );

        $archive->close();
    }
}