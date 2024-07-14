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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Filter\OutputFilter;
use Joomla\String\StringHelper;

class ProjectModel extends AdminModel
{
    public function save($data): bool
    {
        if (empty($data['alias'])) {
            [$title, $alias] = $this->generateProjectTitle($data['title'], $data['alias']);
            $data['title'] = $title;
            $data['alias'] = $alias;
        }

        if (!(int) $data['id']) {
            $data['created_by'] = Factory::getApplication()->getIdentity()->id;
        }

        $data['modified_by'] = Factory::getApplication()->getIdentity()->id;

        return parent::save($data);
    }

    protected function generateProjectTitle($title, $alias)
    {
        // Alter the title & alias
        $table      = $this->getTable();
        $aliasField = $table->getColumnAlias('alias');
        $titleField = $table->getColumnAlias('title');

        if (empty($alias)) {
            if (Factory::getApplication()->get('unicodeslugs') == 1) {
                $alias = OutputFilter::stringUrlUnicodeSlug($title);
            } else {
                $alias = OutputFilter::stringURLSafe($title);
            }
        }

        while ($table->load([$aliasField => $alias])) {
            if ($title === $table->$titleField) {
                $title = StringHelper::increment($title);
            }

            $alias = StringHelper::increment($alias, 'dash');
        }

        return [$title, $alias];
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_hopper.project', 'project', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_hopper.edit.project.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    public function getTable($name = '', $prefix = '', $options = array())
    {
        $name = 'Projects';
        $prefix = 'Table';

        if ($table = $this->_createTable($name, $prefix, $options)) {
            return $table;
        }

        throw new \Exception(Text::sprintf('JLIB_APPLICATION_ERROR_TABLE_NAME_NOT_SUPPORTED', $name), 0);
    }
}