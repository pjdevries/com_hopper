<?php
/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Hopper\Administrator\Extension\Joomla;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Component\ComponentHelper as ComponentHelperCore;
use Joomla\CMS\Factory as JoomlaFactory;

use function defined;

class ComponentHelper extends ComponentHelperCore
{
    public static function getComponentVersion(string $component): ?string
    {
        $db = JoomlaFactory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->qn('manifest_cache'))
            ->from($db->qn('#__extensions'))
            ->where($db->qn('element') . ' = ' . $db->q($component))
            ->where($db->qn('type') . ' = ' . $db->q('component'));

        try {
            $result = $db->setQuery($query)->loadResult();
            $manifestCache = json_decode($result, null, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            return null;
        }

        if (empty($manifestCache?->version ?? '')) {
            return null;
        }

        return $manifestCache->version;
    }
}