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

use Obix\Component\Hopper\Administrator\Extension\Joomla\ComponentHelper;
use Obix\Component\Hopper\Administrator\Extension\Package\Manifest\Manifest;
use Obix\Component\Hopper\Administrator\Extension\Package\PackageHelper;
use ZipArchive;

use function defined;

defined('_JEXEC') or die;

class Package
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
        $componentVersion = ComponentHelper::getComponentVersion('com_hopper');

        $this->manifest->generate(
            $this->packageHelper->manifestTemplatesFolder() . '/package_manifest.template.xml',
            $this->packageHelper->packagesFolder() . '/' . $this->packageHelper->packageManifestName()
        );

        $archive = new ZipArchive();

        $archive->open(
            $this->packageHelper->packagesFolder() . '/' . $this->packageHelper->packageName(),
            ZipArchive::OVERWRITE | ZipArchive::CREATE
        );

        $archive->addFile(
            $this->packageHelper->packagesFolder() . '/' . $this->packageHelper->packageManifestName(),
            $this->packageHelper->packageManifestName()
        );
        $archive->addFile(
            $this->packageHelper->packagesFolder() . '/' . $this->packageHelper->componentPackageName(
                $componentVersion
            ),
            $this->packageHelper->componentPackageName($componentVersion)
        );
        $archive->addFile(
            $this->packageHelper->packagesFolder() . '/' . $this->packageHelper->filesPackageName(),
            $this->packageHelper->filesPackageName()
        );
        $archive->addFile($this->packageHelper->scriptsFolder() . '/package_script.php', 'script.php');

        $archive->close();
    }
}