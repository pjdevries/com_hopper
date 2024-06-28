<?php
/**
 * @package     com_handover
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Handover\Administrator\Extension\Handover\Importer;

use Joomla\CMS\Factory;

\defined('_JEXEC') or die;

class ImporterFactory
{
    public static function createImporter(string $type): ImporterInterface
    {
        return match($type) {
            'categories' => new CategoriesImporter(Factory::getApplication()->bootComponent('com_categories')
                ->getMVCFactory()->createModel('Category', 'Administrator', ['ignore_request' => true]))
        };
    }
}