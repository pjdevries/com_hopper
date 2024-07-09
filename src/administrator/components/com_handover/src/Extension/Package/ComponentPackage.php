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
use Obix\Component\Handover\Administrator\Extension\Settings;
use ZipArchive;

\defined('_JEXEC') or die;

class ComponentPackage
{
    private Settings $settings;

    /**
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function create(): void
    {
        error_clear_last();

        $version = ComponentHelper::getComponentVersion('com_handover');

        $archive = new ZipArchive();

        $archive->open($this->settings->packagesFolder() . '/com_handover-' . $version . '.zip', ZipArchive::OVERWRITE | ZipArchive::CREATE);

        $archive->addFile(JPATH_COMPONENT_ADMINISTRATOR . '/handover.xml', 'handover.xml');
        $this->zipDir($archive, JPATH_ROOT. '/administrator/components/com_handover', 'administrator/components/com_handover');
        // Folder packages is excluded in zipDir because we only want the template files from it.
        $archive->addGlob(JPATH_ROOT. '/administrator/components/com_handover/packages/manifest_templates/*', 0, ['add_path' => 'administrator/components/com_handover/packages/manifest_templates/', 'remove_all_path' => TRUE]);
        $this->zipDir($archive, JPATH_ROOT . '/media/com_handover', 'media/com_handover');

        $archive->close();
    }

    private function zipDir(ZipArchive $archive, string $srcFolder, string $archiveFolder = null): void
    {
        static $ignore = [
            '/\.gitignore$/',
            '/\.git$/',
            '/\.zip$/',
            '/packages$/'
        ];
        $pregMatchAny = static function (array $patterns, string $subject): bool {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $subject) === 1) {
                    return true;
                }
            }

            return false;
        };

        if ($archiveFolder === null) {
            $archiveFolder = $srcFolder;
        }

        $archive->addEmptyDir($archiveFolder);

        foreach (new \FilesystemIterator($srcFolder) as $entry) {
            if ($pregMatchAny($ignore, $entry->getPathname())) {
                continue;
            }

            if ($entry->isDir()) {
                $archive->addEmptyDir($archiveFolder . '/' . $entry->getFilename());
                $this->zipDir($archive, $entry->getPathname(), str_replace($srcFolder, $archiveFolder, $entry->getPathname()));

                continue;
            }

            if ($entry->isFile()) {
                $archive->addFile($entry->getPathname(), $archiveFolder . '/' . $entry->getFilename());
            }
        }
    }
}