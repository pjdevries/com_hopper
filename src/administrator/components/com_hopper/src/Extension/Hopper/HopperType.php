<?php
/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Hopper\Administrator\Extension\Hopper;

enum HopperType: string
{
    case FieldCategories = 'fields_categories';
    case Categories = 'categories';
    case FieldGroups = 'fields_groups';
    case Fields = 'fields';

    public function toFileName(): string
    {
        return $this->value . '.json';
    }
}
