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

use Obix\Component\Handover\Administrator\Extension\Joomla\ComponentHelper;
use Obix\Component\Handover\Administrator\Extension\Package\Manifest\Manifest;
use Obix\Component\Handover\Administrator\Extension\Package\Manifest\PackageManifestAttributes;
use Obix\Component\Handover\Administrator\Extension\Settings;
use ZipArchive;

\defined('_JEXEC') or die;

class Package
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
        $componentVersion = ComponentHelper::getComponentVersion('com_handover');

        (new Manifest(new PackageManifestAttributes($version)))->generate(
            $this->settings->manifestTemplatesFolder() . '/package_manifest.template.xml',
            $this->settings->packagesFolder() . '/pkg_handover.xml'
        );

        $archive = new ZipArchive();

        $archive->open($this->settings->packagesFolder() . '/pkg_handover-' . $version . '.zip', ZipArchive::OVERWRITE | ZipArchive::CREATE);

        $archive->addFile($this->settings->packagesFolder() . '/pkg_handover.xml',  'pkg_handover.xml');
        $archive->addFile($this->settings->packagesFolder() . '/com_handover-' . $componentVersion . '.zip',  'com_handover-' . $componentVersion . '.zip');
        $archive->addFile($this->settings->packagesFolder() . '/handover_import_files-' . $version . '.zip',  'handover_import_files-' . $version . '.zip');

        $archive->close();
    }
}