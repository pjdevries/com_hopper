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

use Joomla\CMS\Factory;

\defined('_JEXEC') or die;

class Settings
{
    /**
     * The base folder under which the export and import stuff takes place, (temporary) files are stored,
     * manifest files and packages are generated, etc.
     *
     * @return string
     */
    public function packagesFolder(): string
    {
        return JPATH_ADMINISTRATOR . '/components/com_hopper/packages';
    }

    /**
     * The folder into which the relevant export files placed.
     *
     * @return string
     */
    public function exportFolder(string $version): string
    {
        return self::packagesFolder() . '/export/' . $version;
    }

    /**
     * The folder to which the import files are exported.
     *
     * @return string
     */
    public function exportFilesFolder(string $version): string
    {
        return self::exportFolder($version) . '/files';
    }

    public function importFolder(string $version): string
    {
        return self::packagesFolder() . '/import/' . $version;
    }

    public function importFilesFolder(string $version): string
    {
        return self::importFolder($version) . '/files';
    }

    public function manifestTemplatesFolder(): string
    {
        return self::packagesFolder() . '/manifest_templates';
    }

    public function scriptsFolder(): string
    {
        return self::packagesFolder() . '/scripts';
    }
}