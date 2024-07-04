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

use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

class Handler
{
    const ALL = 0;
    const SUCCESFUL = 1;
    const FAILED = 2;

    private array $uploadedFiles;

    private Prerequisites $prerequisites;

    /**
     * @param array $uploadedFiles
     */
    public function __construct(array $uploadedFiles = [], Prerequisites $prerequisites = new Prerequisites())
    {
        $this->uploadedFiles = $uploadedFiles;
        $this->prerequisites = $prerequisites;
    }

    public function execute(): array
    {
        foreach ($this->uploadedFiles as $files) {
            $failureCount = $this->check();

            if ($failureCount < count($files)) {
                $this->save();
            }
        }

        return $this->getSuccesful();
    }

    /**
     * @param array $uploadedFiles
     * @param Form $form
     * @param bool $onlyReturnSuccessful
     * @return Handler[]
     */
    public static function handle(array $uploadedFiles, Form $form, bool $onlyReturnSuccessful = false): array
    {
        static $validBoolean = [
            '0' => false,
            '1' => true,
            'false' => false,
            'true' => true,
            'no' => false,
            'yes' => true
        ];

        $handlers = [];

        // Extract custom field settings for all fields of type "upload" from the form definition.
        $uploadFieldSpecsByFieldName = array_reduce($form->getFieldset(), function (array $carry, FormField $field) use ($validBoolean) {
            if (strtolower($field->getAttribute('type')) === 'upload') {
                // File input name is field name with '-files' suffix appended.
                $carry[$field->getProperty('fieldname') . '-files'] = [
                    'maxUploadSize' => $field->getAttribute('maxUploadSize') ?? $field->getProperty('maxUploadSize'),
                    'destDir' => $field->getAttribute('destDir') ?? $field->getProperty('destDir'),
                    'replaceIfExists' => $validBoolean[($field->getAttribute('replaceIfExists') ?? $field->getProperty(
                            'replaceIfExists'
                        ))] ?? false,
                ];
            }

            return $carry;
        }, []);

        foreach ($uploadedFiles as $fieldName => $files) {
            $fieldSpecs = $uploadFieldSpecsByFieldName[$fieldName];
            $prerequisites = new Prerequisites($fieldSpecs['destDir'], $fieldSpecs['maxUploadSize'], [], $fieldSpecs['replaceIfExists']);

            $uploadHandler = new static($files, $prerequisites);
            $uploadHandler->execute();

            $handlers[$fieldName] = $uploadHandler;
        }

        return $handlers;
    }

    public function check(): int
    {
        $failureCount = 0;

        foreach ($this->uploadedFiles as $key => $uploadedFile) {
            try {
                $this->checkFile($uploadedFile);
            } catch (\RuntimeException $e) {
                $this->uploadedFiles[$key]['exception'] = $e;
                $failureCount++;
            }
        }

        return $failureCount;
    }

    private function checkFile(array $uploadedFile)
    {
        if (!isset($uploadedFile['error']) || is_array($uploadedFile['error'])) {
            throw new \RuntimeException(Text::_('COM_DMA_SHIP_FOTO_S_UPLOAD_ERROR'));
        }

        switch ($uploadedFile['error']) {
            case UPLOAD_ERR_OK:
                break;

            case UPLOAD_ERR_NO_FILE:
                throw new \RuntimeException(Text::_('COM_DMA_SHIP_FOTO_S_UPLOAD_ERROR_NO_FILE'));

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \RuntimeException(
                    Text::sprintf(
                        'COM_DMA_SHIP_FOTO_S_UPLOAD_ERROR_MAX_SIZE_EXCEEDED',
                        $this->prerequisites->getMaxFileSize()
                    )
                );

            default:
                throw new \RuntimeException(Text::_('COM_DMA_SHIP_FOTO_S_UPLOAD_ERROR'));
        }

        if (!is_file($uploadedFile['tmp_name'])) {
            throw new \RuntimeException(Text::_('COM_DMA_SHIP_FOTO_S_UPLOAD_NOT_FOUND'));
        }

        if (!$this->prerequisites->isValidSize($uploadedFile['size'])) {
            throw new \RuntimeException(
                Text::sprintf(
                    'COM_DMA_SHIP_FOTO_S_UPLOAD_ERROR_MAX_SIZE_EXCEEDED',
                    $this->prerequisites->getMaxFileSize()
                )
            );
        }

        $uploadMimeType = $this->mimeType($uploadedFile['tmp_name']);

        if (!$this->prerequisites->isValidMimeType($uploadMimeType)) {
            throw new \RuntimeException(
                Text::sprintf('COM_DMA_SHIP_FOTO_S_UPLOAD_ERROR_INVALID_TYPE', $uploadMimeType)
            );
        }
    }

    public function failedFiles(): array
    {
        // Files that didn't survive the validation contain an exception element.
        return array_filter($this->uploadedFiles, function (array $file) {
            return isset($file['exception']);
        });
    }

    public function getFailed(): array
    {
        return $this->getProcessed(self::FAILED);
    }

    public function getSuccesful(): array
    {
        return $this->getProcessed(self::SUCCESFUL);
    }

    public function getProcessed(int $which = self::ALL): array
    {
        return match ($which) {
            self::SUCCESFUL => array_filter($this->uploadedFiles, fn($u) => !isset($file['exception'])),
            self::FAILED => array_filter($this->uploadedFiles, fn($u) => isset($file['exception'])),
            default => $this->uploadedFiles
        };
    }

    public function save(): int
    {
        $failureCount = 0;

        foreach ($this->getSuccesful() as $key => $file) {
            try {
                // Move the uploaded file to the final destination.
                $this->uploadedFiles[$key]['dest_path'] = $this->move($file);
            } catch (\Exception $e) {
                $this->uploadedFiles[$key]['exception'] = $e;
                $failureCount++;
            }
        }

        return $failureCount;
    }

    public function move(array &$uploadedFile): string
    {
        $srcPath = $uploadedFile['tmp_name'];

        if (!is_uploaded_file($srcPath)) {
            throw new \RuntimeException(Text::_('COM_DMA_ERROR_NOT_AN_UPLOADED_FILE'));
        }

        $destDir = $this->prerequisites->getDestDir();

        if (!file_exists($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $destPath = realpath($destDir) . '/' . $uploadedFile['name'];

        $regex = '/^(?P<name>.*?) \((?P<version>([0-9]+))\)$/';

        if (file_exists($destPath) && !$this->prerequisites->isReplaceIfExists()) {
            $version = 1;

            do {
                $parts = pathinfo($destPath);

                if (preg_match($regex, $parts['filename'], $matches) === 1 && isset($matches['version'])) {
                    $version = max($version, (int)($matches['version'] + 1));
                }

                $destPath = $parts['dirname'] . '/' . (($matches['name'] ?? '') ?: $parts['filename']);

                if ($version > 0) {
                    $destPath .= ' (' . $version . ')';
                }

                if (!empty($parts['extension'])) {
                    $destPath .= '.' . $parts['extension'];
                }
            } while (file_exists($destPath));
        }

        if (!rename($srcPath, $destPath)) {
            throw new \RuntimeException(error_get_last()['message']);
        }

        if (!chmod($destPath, 0644)) {
            unlink($destPath);

            throw new \RuntimeException(error_get_last()['message']);
        }

        return $destPath;
    }

    public function remove(): void
    {
        foreach ($this->getSuccesful() as $file) {
            if (file_exists($file['dest_path'])) {
                unlink($file['dest_path']);
            }
        }
    }

    private function mimeType(string $file): string
    {
        return (new \finfo(FILEINFO_MIME_TYPE))->file($file);
    }

    /**
     * @return array
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }
}