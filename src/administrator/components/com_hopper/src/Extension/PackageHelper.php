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
    /**
     * @var string
     */
    private string $projectAlias;

    /**
     * @var string
     */
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
     * The export base folder.
     *
     * @return string
     */
    public function exportFolder(): string
    {
        return self::packagesFolder() . '/export';
    }

    /**
     * The target folder for the exported import files.
     *
     * @return string
     */
    public function exportFilesFolder(): string
    {
        return self::exportFolder() . '/files';
    }

    /**
     * The base import folder.
     *
     * @return string
     */
    public function importFolder(): string
    {
        return self::packagesFolder() . '/import';
    }

    /**
     * The target folder for the installed/uploaded import files.
     *
     * @return string
     */
    public function importFilesFolder(): string
    {
        return self::importFolder() . '/files';
    }

    /**
     * The folder where the manifest file templates are.
     *
     * @return string
     */
    public function manifestTemplatesFolder(): string
    {
        return JPATH_ADMINISTRATOR . '/components/com_hopper/packages/manifest_templates';
    }

    /**
     * The folder where the package scripts are.
     *
     * @return string
     */
    public function scriptsFolder(): string
    {
        return JPATH_ADMINISTRATOR . '/components/com_hopper/packages/scripts';
    }

    /**
     * @param string $version
     * @return string
     */
    public function componentPackageName(string $version): string
    {
        return 'com_hopper-' . $version . '.zip';
    }

    /**
     * @return string
     */
    public function filesManifestName(): string
    {
        return 'hopper_' . $this->projectAlias . '_import_files-' . $this->getVersion() . '.xml';
    }

    /**
     * @return string
     */
    public function filesPackageName(): string
    {
        return 'hopper_' . $this->projectAlias . '_import_files-' . $this->getVersion() . '.zip';
    }

    /**
     * @return string
     */
    public function packageManifestName(): string
    {
        return 'pkg_hopper_' . $this->projectAlias . '-' . $this->version . '.xml';
    }

    /**
     * @return string
     */
    public function packageName(): string
    {
        return 'pkg_hopper_' . $this->projectAlias . '-' . $this->version . '.zip';
    }

    /**
     * @return string
     */
    public function getProjectAlias(): string
    {
        return $this->projectAlias;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}