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

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Component\SustainabilityCalculator\Administrator\Measurement\Converter;
use Joomla\Component\SustainabilityCalculator\Administrator\Measurement\Unit;

use function defined;

class ReleaseModel extends AdminModel
{
    public function maxProjectVersion(int $projectId): string
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query
            ->select('MAX(' . $db->quoteName('version') . ') AS ' . $db->quoteName('version'))
            ->from($db->quoteName('#__hopper_releases'))
            ->where($db->quoteName('project_id') . ' = ' . $db->quote($projectId));
        $db->setQuery($query);

        return $db->loadResult();
    }

    public function save($data): bool
    {
        if (!(int)$data['id']) {
            $data['created_by'] = Factory::getApplication()->getIdentity()->id;
        }

        $data['modified_by'] = Factory::getApplication()->getIdentity()->id;

        if (!($result = parent::save($data))) {
            return $result;
        }

        return true;
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_hopper.release', 'release', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

//        $this->setDynamicFieldAttributes($form);

        return $form;
    }
    private function setDynamicFieldAttributes(Form $form, string $measurmentUnit = 'mm'): void
    {
        $version = ($this->maxProjectVersion() ?? '') ?: '1.0.0';

        $form->setFieldAttribute(
            'version',
            'default',
            $version
        );
    }

    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_hopper.edit.release.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    public function getTable($name = '', $prefix = '', $options = array())
    {
        $name = 'Releases';
        $prefix = 'Table';

        if ($table = $this->_createTable($name, $prefix, $options)) {
            return $table;
        }

        throw new Exception(Text::sprintf('JLIB_APPLICATION_ERROR_TABLE_NAME_NOT_SUPPORTED', $name), 0);
    }
}