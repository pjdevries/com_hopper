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

use Joomla\Component\Fields\Administrator\Model\FieldModel;

\defined('_JEXEC') or die;

class Fields implements ImporterInterface
{
    private FieldModel $model;

    private array $idMap = [];

    private array $groupIdMap = [];

    private array $categoryIdMap = [];

    private array $fieldCategories = [];

    public function __construct(FieldModel $model)
    {
        $this->model = $model;
    }

    public function import(array $data): void
    {
        foreach ($data as $field) {
            if (!($id = $this->save($field))) {
                throw new \RuntimeException($this->model->getError());
            }

            $this->idMap[(int)$field->id] = $id;
        }
    }

    private function save(object $field): int
    {
        $groupId = $this->groupIdMap[$field->group_id] ?? 0;
        $categories = array_map(fn(int $orgId) => $this->categoryIdMap[$orgId],
            $this->fieldCategories[$field->id] ?? []);

        $data = [
            'id' => 0,
            'context' => $field->context,
            'group_id' => $groupId,
            'assigned_cat_ids' => $categories,
            'title' => $field->title,
            'name' => $field->name,
            'type' => $field->type,
            'required' => $field->required,
            'only_use_in_subform' => $field->only_use_in_subform,
            'default_value' => $field->default_value,
            'state' => $field->state,
            'language' => $field->language,
            'note' => $field->note,
            'label' => $field->label,
            'description' => $field->description,
            'access' => $field->access,
            'rules' => [],
            'params' => json_encode($field->params),
            'fieldparams' => json_encode($field->fieldparams),
            'tags' => []
        ];

        return $this->model->save($data) ? (int)$this->model->getState($this->model->getName() . '.id') : 0;
    }

    public function getIdMap(): array
    {
        return $this->idMap;
    }

    public function setGroupIdMap(array $groupIdMap): Fields
    {
        $this->groupIdMap = $groupIdMap;
        return $this;
    }

    public function setCategoryIdMap(array $categoryIdMap): Fields
    {
        $this->categoryIdMap = $categoryIdMap;
        return $this;
    }

    public function setFieldCategories(array $fieldCategories): Fields
    {
        $this->fieldCategories = $fieldCategories;
        return $this;
    }
}