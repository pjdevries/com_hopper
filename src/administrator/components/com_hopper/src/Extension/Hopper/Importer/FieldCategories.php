<?php
/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Hopper\Administrator\Extension\Hopper\Importer;

use Joomla\Component\Fields\Administrator\Model\GroupModel;
use Obix\Component\Hopper\Administrator\Extension\Hopper\Importer\ImporterInterface;

\defined('_JEXEC') or die;

class FieldCategories implements ImporterInterface
{
    private GroupModel $model;

    public function import(array $data): void
    {
        // TODO: Implement import() method.
    }
}