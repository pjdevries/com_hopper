<?php
/**
 * @package     WebwinkelKeur
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Obix\Component\Hopper\Administrator\Extension\Hopper\HopperType;
use Obix\Component\Hopper\Administrator\Extension\Pathname;

return new class() implements InstallerScriptInterface {
    private string $minimumJoomla = '5.0';
    private string $minimumPhp = '8.2';

    public function install(InstallerAdapter $adapter): bool
    {
        // TODO: Implement install() method.
        return true;
    }

    public function update(InstallerAdapter $adapter): bool
    {
        // TODO: Implement update() method.
        return true;
    }

    public function uninstall(InstallerAdapter $adapter): bool
    {
        // TODO: Implement uninstall() method.
        return true;
    }

    public function preflight(string $type, InstallerAdapter $adapter): bool
    {
        // TODO: Implement preflight() method.
        return true;
    }

    public function postflight(string $type, InstallerAdapter $adapter): bool
    {
        // Import installed import files.
        $settings = new Pathname();
        $version = (string)$adapter->manifest->version;

        $importFolder = $settings->importFilesFolder($version);

        // File names are constructed from Hopper types.
        $installedFiles = glob($importFolder . '/*.json');
        $filesToImport = [];

        foreach ($installedFiles as $file) {
            $type = basename($file, '.json');

            if (HopperType::tryFrom($type)) {
                $filesToImport[HopperType::from($type)->value] = $file;
            }
        }

        /** @var \Obix\Component\Hopper\Administrator\Model\ImportModel $model */
        $model = Factory::getApplication()->bootComponent('com_hopper')->getMVCFactory()->createModel(
            'Import',
            'Administrator',
            ['ignore_request' => true]
        );

        $model->importFiles($filesToImport, $importFolder, $installedFiles);

        return true;
    }
};
