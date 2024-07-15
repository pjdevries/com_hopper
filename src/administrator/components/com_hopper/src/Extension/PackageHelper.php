<?php
/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Hopper\Administrator\Extension;

\defined('_JEXEC') or die;

class PackageHelper
{
    private string $projectAlias;

    private string $version;

    public function __construct(string $projectAlias, string $version) {
        $this->projectAlias = $projectAlias;
        $this->version = $version;
    }

    /**
     * The base folder under which the export and import stuff takes place, (temporary) files are stored,
     * manifest files and packages are generated, etc.
     *
     * @return string
     */
    public function packagesFolder(): string
    {
        return JPATH_ADMINISTRATOR . '/components/com_hopper/packages/' . $this->projectAlias . '/' . $this->version;
    }

    /**
     * The folder into which the relevant export files placed.
     *
     * @return string
     */
    public function exportFolder(): string
    {
        return self::packagesFolder() . '/export/' . $this->version;
    }

    /**
     * The folder to which the import files are exported.
     *
     * @return string
     */
    public function exportFilesFolder(): string
    {
        return self::exportFolder() . '/files';
    }

    public function importFolder(): string
    {
        return self::packagesFolder() . '/import';
    }

    public function importFilesFolder(): string
    {
        return self::importFolder() . '/files';
    }

    public function manifestTemplatesFolder(): string
    {
        return JPATH_ADMINISTRATOR . '/components/com_hopper/packages/manifest_templates';
    }

    public function scriptsFolder(): string
    {
        return JPATH_ADMINISTRATOR . '/components/com_hopper/packages/scripts';
    }

    public function componentPackageName(string $version): string
    {
        return 'com_hopper-' . $version . '.zip';
    }

    public function filesManifestName(): string
    {
        return 'hopper_' . $this->projectAlias . '_import_files-' . $this->getVersion() . '.xml';
    }

    public function filesPackageName(): string
    {
        return 'hopper_' . $this->projectAlias . '_import_files-' . $this->getVersion() . '.zip';
    }

    public function packageManifestName(): string
    {
        return 'pkg_hopper_' . $this->projectAlias . '-' . $this->version . '.xml';
    }

    public function packageName(): string
    {
        return 'pkg_hopper_' . $this->projectAlias . '-' . $this->version . '.zip';
    }

    public function getProjectAlias(): string
    {
        return $this->projectAlias;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}