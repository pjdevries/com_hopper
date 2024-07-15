<?php
/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Hopper\Administrator\Extension\Package\Manifest;

use function defined;

defined('_JEXEC') or die;

class Manifest
{
    private ManifestAttributesInterface $attributes;

    /**
     * @param ManifestAttributesInterface $attributes
     */
    public function __construct(ManifestAttributesInterface $attributes)
    {
        $this->attributes = $attributes;
    }

    public function generate(string $templatePath, string $ouputPath): void
    {
        $template = file_get_contents($templatePath);
        $attributes = $this->attributes->getAttributes();
        $output = str_replace(array_map(fn(string $tag) => '{{' . $tag . '}}', array_keys($attributes)),
            array_values($attributes),
            $template);
        file_put_contents($ouputPath, $output);
    }
}