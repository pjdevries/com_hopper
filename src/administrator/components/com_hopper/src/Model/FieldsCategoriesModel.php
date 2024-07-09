<?php
/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Hopper\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\ListModelInterface;

use function defined;

class FieldsCategoriesModel extends BaseDatabaseModel implements ListModelInterface
{
    public function getItems(): ?array
    {
        $db = $this->getDatabase();

        $query = $db->getQuery(true);
        $query
            ->select($db->quoteName(['field_id', 'category_id']))
            ->from($db->quoteName('#__fields_categories'));
        $db->setQuery($query);

        return $db->loadObjectList();
    }
}