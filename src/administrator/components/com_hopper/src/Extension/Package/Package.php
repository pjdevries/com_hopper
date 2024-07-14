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
use Obix\Component\Hopper\Administrator\Extension\Package\Manifest\PackageManifestAttributes;
use Obix\Component\Hopper\Administrator\Extension\Pathname;
use ZipArchive;

\defined('_JEXEC') or die;

class Package
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
        $componentVersion = ComponentHelper::getComponentVersion('com_hopper');

        $this->manifest->generate(
            $this->settings->manifestTemplatesFolder($projectAlias) . '/package_manifest.template.xml',
            $this->settings->packagesFolder($projectAlias) . '/pkg_hopper.xml'
        );

        $archive = new ZipArchive();

        $archive->open($this->settings->packagesFolder($projectAlias) . '/pkg_hopper-' . $version . '.zip', ZipArchive::OVERWRITE | ZipArchive::CREATE);

        $archive->addFile($this->settings->packagesFolder($projectAlias) . '/pkg_hopper.xml',  'pkg_hopper.xml');
        $archive->addFile($this->settings->packagesFolder($projectAlias) . '/com_hopper-' . $componentVersion . '.zip',  'com_hopper-' . $componentVersion . '.zip');
        $archive->addFile($this->settings->packagesFolder($projectAlias) . '/hopper_import_files-' . $version . '.zip',  'hopper_import_files-' . $version . '.zip');
        $archive->addFile($this->settings->scriptsFolder($projectAlias) . '/package_script.php',  'script.php');

        $archive->close();
    }
}