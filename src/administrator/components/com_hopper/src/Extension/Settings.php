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
        return JPATH_COMPONENT_ADMINISTRATOR . '/packages';
    }

    /**
     * The folder into which the relevant export files placed.
     *
     * @return string
     */
    public function exportFolder(): string
    {
        return self::packagesFolder() . '/export';
    }

    /**
     * The folder to which the import files are exported.
     *
     * @return string
     */
    public function exportFilesFolder(): string
    {
        return self::packagesFolder() . '/export/files';
    }

    public function importFolder(): string
    {
        return self::packagesFolder() . '/import';
    }

    public function importFilesFolder(): string
    {
        return self::packagesFolder() . '/import/files';
    }

    public function manifestTemplatesFolder(): string
    {
        return self::packagesFolder() . '/manifest_templates';
    }
}