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

use Joomla\Component\Categories\Administrator\Helper\CategoriesHelper;
use Joomla\Component\Categories\Administrator\Model\CategoriesModel as BaseCategoriesModel;

use function defined;
use function is_array;

class CategoriesModel extends BaseCategoriesModel
{
    public function getItems(int $limitToFieldCategories = 0)
    {
        $this->setState('filter.limit_to_field_categories', $limitToFieldCategories);

        $this->limitToFieldCategories = $limitToFieldCategories;

        $items = parent::getItems();

        foreach ($items as &$item) {
            $item->associations = $this->getAssociations($item);
        }

        $items = $this->addParents($items);

        usort($items, fn(object $a, object $b) => $a->parent_id <=> $b->parent_id);

        return $items;
    }

    private function addParents(array $items): array
    {
        $parentIds = array_unique(
            array_map(fn(object $item) => $item->parent_id,
                array_filter($items, fn(object $item) => $item->parent_id > 1))
        );

        if (!count($parentIds)) {
            return $items;
        }

        $this->setState('filter.id', $parentIds);

        if ($parents = $this->getItems()) {
            $items = array_merge($items, $this->addParents($parents));
        }

        return $items;
    }

    private function getAssociations(object $item): object
    {
        $associations = [];

        foreach (CategoriesHelper::getAssociations($item->id, $item->extension) as $lang => $association) {
            [$associationId, $associationAlias] = explode(':', $association, 2);
            $associations[$lang] = $associationId;
        }

        return (object)$associations;
    }

    public function getListQuery()
    {
        $db = $this->getDatabase();

        $query = parent::getListQuery();
        $query->select('\'{}\' AS associations');

        $limitToFieldCategories = (int)$this->getState('filter.limit_to_field_categories', 0);

        if ($limitToFieldCategories) {
            $subQuery = $db->getQuery(true)
                ->select('DISTINCT ' . $db->quoteName('category_id'))
                ->from($db->quoteName('#__fields_categories'));

            $query->join(
                'INNER',
                '(' . $subQuery . ') AS ' . $db->quoteName('fg'),
                $db->quoteName('fg.category_id') . ' = ' . $db->quoteName('a.id')
            );
        }

        $id = $this->getState('filter.id', []);

        if (!is_array($id)) {
            $id = $id ? [$id] : [];
        }

        if (count($id)) {
            $query->where($db->quoteName('a.id') . ' IN (' . implode(',', $id) . ')');
        }

        return $query;
    }

    protected function getStoreId($id = '')
    {
        // Add the list state to the store id.
        $id .= ':' . $this->getState('filter.for_fields');
        $id .= ':' . serialize($this->getState('filter.id', []));
        $id .= ':' . $this->getState('list.start');
        $id .= ':' . $this->getState('list.limit');
        $id .= ':' . $this->getState('list.ordering');
        $id .= ':' . $this->getState('list.direction');

        return md5($this->context . ':' . $id);
    }
}