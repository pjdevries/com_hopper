<?php
/**
 * @package     com_handover
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Handover\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ModelInterface;
use Joomla\Component\Categories\Administrator\Helper\CategoriesHelper;
use Joomla\Component\Categories\Administrator\Model\CategoriesModel as BaseCategoriesModel;
use Joomla\Component\Categories\Administrator\Model\CategoryModel;

class CategoriesModel extends BaseCategoriesModel
{
    private CategoryModel|ModelInterface $categoryModel;

    public function __construct($config = [], MVCFactoryInterface $factory = null)
    {
        parent::__construct($config, $factory);

        $this->categoryModel = Factory::getApplication()->bootComponent('com_categories')
            ->getMVCFactory()->createModel('Category', 'Administrator', ['ignore_request' => true]);
    }

    public function getItems()
    {
        $items = parent::getItems();

        foreach ($items as &$item) {
            $item->associations = $this->getAssociations($item);
        }

        $items = array_merge($items, $this->getParents($items));

        usort($items, fn(object $a, object $b) => $a->parent_id <=> $b->parent_id);

        return $items;
    }

    private function getParents(array $items, array &$parents = []): array
    {
        $parents = [];

        $parentIds = array_unique(
            array_map(fn(object $item) => $item->parent_id,
                array_filter($items, fn(object $item) => $item->parent_id > 1))
        );

        if (!count($parentIds)) {
            return $items;
        }

        $moreParents = array_map(fn(int $parentId) => (object)((array)$this->categoryModel->getItem($parentId)), $parentIds);

        if (count($moreParents)) {
            $parents = array_merge($parents, $this->getParents($moreParents));
        }

        return $parents;
    }

    private function getAssociations(object $item): array
    {
        $associations = [];

        foreach (CategoriesHelper::getAssociations($item->id, $item->extension) as $lang => $association) {
            [$associationId, $associationAlias] = explode(':', $association, 2);
            $associations[$lang] = $associationId;
        }

        return $associations;
    }

    public function getListQuery()
    {
        $db = $this->getDatabase();

        $subQuery = $db->getQuery(true)
            ->select('DISTINCT ' . $db->quoteName('category_id'))
            ->from($db->quoteName('#__fields_categories'));

        $query = parent::getListQuery();
        $query->select('\'{}\' AS associations');
        $query->join(
            'INNER',
            '(' . $subQuery . ') AS ' . $db->quoteName('fg'),
            $db->quoteName('fg.category_id') . ' = ' . $db->quoteName('a.id')
        );

        return $query;
    }

    protected function getStoreId($id = '')
    {
        // Add the list state to the store id.
        $id .= ':' . serialize($this->getState('filter.category_id', []));
        $id .= ':' . $this->getState('list.start');
        $id .= ':' . $this->getState('list.limit');
        $id .= ':' . $this->getState('list.ordering');
        $id .= ':' . $this->getState('list.direction');

        return md5($this->context . ':' . $id);
    }
}