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

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Component\Categories\Administrator\Model\CategoryModel;

class CategoriesImporter extends BaseImporter implements ImporterInterface
{
    private CategoryModel $model;

    private array $idMap = [];

    public function __construct(CategoryModel $model)
    {
        $this->model = $model;
        $this->model->setState('category.extension', 'com_content');
    }

    public function import(array $data): void
    {
        usort($data, fn(object $c1, object $c2) => $c1->parent <=> $c2->parent);

        foreach ($data as $category) {
            $category->parent_id = $this->idMap[$category->parent_id] ?? 1;

            if (!($id = $this->save($category))) {
                throw new \RuntimeException($this->model->getError());
            }

            $this->idMap[(int) $category->id] = $id;
        }
        
        $this->fixAssociations($data, $this->idMap);
    }
    
    private function fixAssociations(array $data, array $idMap): void
    {
        foreach ($data as $category) {
            $data = [
                'associations' => array_reduce(array_keys((array)$category->associations), function (array $carry, string $lang) use ($category, $idMap) {
                    $carry[$lang] = $idMap[(int)$category->associations->$lang];

                    return $carry;
                }, [])
            ];

            if (!$this->model->save($data)) {
                throw new \RuntimeException($this->model->getError());
            }
        }
    }

    private function save(object $category): int
    {
        $data = [
            'id' => 0,
            'parent_id' => $category->parent_id,
            'extension' => $category->extension,
            'title' => $category->title,
            'alias' => $category->alias,
            'version_note' => $category->version_note,
            'note' => $category->note,
            'description' => $category->description,
            'published' => $category->published,
            'access' => $category->access,
            'metadesc' => $category->metadesc,
            'metakey' => $category->metakey,
            'language' => $category->language,
            'params' => json_encode($category->params),
            'metadata' => json_encode($category->metadata),
            'associations' => $category->associations,
            'com_fields' => [],
            'tags' => []
        ];

        return $this->model->save($data) ? (int) $this->model->getState($this->model->getName() . '.id') : 0;
    }

    public function getIdMap(): array
    {
        return $this->idMap;
    }
}