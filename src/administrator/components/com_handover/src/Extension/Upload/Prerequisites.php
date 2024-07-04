<?php
/**
 * @package     PIM
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Handover\Administrator\Extension\Upload;

\defined('_JEXEC') or die;

class Prerequisites
{
    private string $destDir;

    private int $maxFileSize = 0;

    private array $validMimeTypes;

    private bool $replaceIfExists;

    /**
     * @param string $maxFileSize
     * @param array $validMimeTypes
     */
    public function __construct(string $destDir = '', string $maxFileSize = '', array $validMimeTypes = [], bool $replaceIfExists = false)
    {
        $this->destDir = rtrim($destDir, '\\/');
        $this->maxFileSize = $this->parseSize($maxFileSize);
        $this->validMimeTypes = $validMimeTypes;
        $this->replaceIfExists = $replaceIfExists;
    }

    /**
     * @param int $maxFileSize
     * @return Prerequisites
     */
    public function setMaxFileSize(string $maxFileSize): Prerequisites
    {
        $this->maxFileSize = $this->parseSize($maxFileSize);

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxFileSize(): int
    {
        static $maxSize = -1;

        if ($maxSize < 0) {
            // Start with post_max_size.
            $postMaxSize = $this->parseSize(ini_get('post_max_size'));

            if ($postMaxSize > 0) {
                $maxSize = $postMaxSize;
            }

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $uploadMaxSize = $this->parseSize(ini_get('upload_max_filesize'));

            if ($uploadMaxSize > 0 && $uploadMaxSize < $maxSize) {
                $maxSize = $uploadMaxSize;
            }

            if ($this->maxFileSize > 0 && $this->maxFileSize < $maxSize) {
                $maxSize = $this->maxFileSize;
            }
        }

        return $maxSize;
    }

    public function isValidSize(int $size): bool
    {
        return ($maxSize = $this->getMaxFileSize()) && $size <= $maxSize;
    }

    /**
     * @param array $validMimeTypes
     * @return Prerequisites
     */
    public function setValidMimeTypes(array $validMimeTypes): Prerequisites
    {
        $this->validMimeTypes = $validMimeTypes;

        return $this;
    }

    public function isValidMimeType(string $type): bool
    {
        return !count($this->validMimeTypes) || in_array($type, $this->validMimeTypes);
    }

    private function parseSize(string $size): int
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.

        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }

    /**
     * @return string
     */
    public function getDestDir(): string
    {
        return $this->destDir;
    }

    /**
     * @param string $destDir
     * @return Prerequisites
     */
    public function setDestDir(string $destDir): Prerequisites
    {
        $this->destDir = rtrim($destDir, '\\/');

        return $this;
    }

    public function isReplaceIfExists(): bool
    {
        return $this->replaceIfExists;
    }

    public function setReplaceIfExists(bool $replaceIfExists): void
    {
        $this->replaceIfExists = $replaceIfExists;
    }
}