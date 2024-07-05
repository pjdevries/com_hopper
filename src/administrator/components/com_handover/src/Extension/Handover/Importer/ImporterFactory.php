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
use Obix\Component\Handover\Administrator\Extension\Handover\HandoverType;

use function defined;

defined('_JEXEC') or die;

class ImporterFactory
{
    public static function createImporter(HandoverType $type): ImporterInterface
    {
        return match ($type) {
            HandoverType::Categories => new Categories(
                Factory::getApplication()->bootComponent('com_categories')
                    ->getMVCFactory()->createModel('Category', 'Administrator', ['ignore_request' => true])
            ),
            HandoverType::FieldGroups => new FieldGroups(
                Factory::getApplication()->bootComponent('com_fields')
                    ->getMVCFactory()->createModel('Group', 'Administrator', ['ignore_request' => true])
            ),
            HandoverType::Fields => new Fields(
                Factory::getApplication()->bootComponent('com_fields')
                    ->getMVCFactory()->createModel('Field', 'Administrator', ['ignore_request' => true])
            )
        };
    }
}