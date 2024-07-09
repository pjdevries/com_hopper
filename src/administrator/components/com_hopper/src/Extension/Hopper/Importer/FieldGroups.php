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

\defined('_JEXEC') or die;

use Joomla\Component\Fields\Administrator\Model\GroupModel;

class FieldGroups implements ImporterInterface
{
    private GroupModel $model;

    private array $idMap = [];

    public function __construct(GroupModel $model)
    {
        $this->model = $model;
    }

    public function import(array $data): void
    {
        foreach ($data as $group) {
            if (!($id = $this->save($group))) {
                throw new \RuntimeException($this->model->getError());
            }

            $this->idMap[(int)$group->id] = $id;
        }
    }

    private function save(object $group): int
    {
        $data = [
            'id' => 0,
            'context' => $group->context,
            'title' => $group->title,
            'state' => $group->state,
            'language' => $group->language,
            'note' => $group->note,
            'description' => $group->description,
            'access' => $group->access,
            'rules' => [],
            'params' => json_encode($group->params),
            'tags' => []
        ];

        return $this->model->save($data) ? (int)$this->model->getState($this->model->getName() . '.id') : 0;
    }

    public function getIdMap(): array
    {
        return $this->idMap;
    }
}