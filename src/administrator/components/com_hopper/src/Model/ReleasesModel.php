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

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\QueryInterface;

class ReleasesModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'release.id',
                'project_id', 'release.project_id',
                'version', 'release.version',
                'title', 'project.title',
                'alias', 'project.alias'
            ];
        }

        parent::__construct($config);
    }

    protected function getListQuery(): QueryInterface|string|DatabaseQuery
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            $this->getState(
                'list.select',
                [
                    $db->quoteName('release.id'),
                    $db->quoteName('release.project_id'),
                    $db->quoteName('release.version'),
                    $db->quoteName('project.title'),
                    $db->quoteName('project.alias')
                ]
            )
        )->from($db->quoteName('#__hopper_releases', 'release'))
            ->join(
                'INNER',
                $db->quoteName('#__hopper_projects', 'project'),
                $db->qn('release.project_id') . ' = ' . $db->quoteName('project.id')
            );

        $search = $this->getState('filter.search');

        if (!empty($search)) {
            $search = $db->quote(
                '%' . str_replace(
                    ' ',
                    '%',
                    $db->escape(trim($search), true) . '%'
                )
            );
            $query->where('(release.title LIKE ' . $search . ')');
        }

        $orderCol = $this->state->get(
            'list.ordering',
            'release.title'
        );
        $orderDirn = $this->state->get(
            'list.direction',
            'ASC'
        );
        $query->order(
            $db->escape($orderCol) . ' ' . $db->
            escape($orderDirn)
        );

        return $query;
    }

    protected function populateState(
        $ordering = 'title',
        $direction = 'ASC'
    ) {
        $app = Factory::getApplication();

        $listLimit = $app->input->get('limit', $app->get('list_limit', 0), 'uint');
        $this->setState('list.limit', $listLimit);

        $limitStart = $app->input->get('limitstart', 0, 'uint');
        $this->setState('list.start', $limitStart);

        $search = $this->getUserStateFromRequest(
            $this->context .
            '.filter.search',
            'filter_search'
        );
        $this->setState('filter.search', $search);

        parent::populateState($ordering, $direction);
    }
}