<?php
/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Component\Hopper\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Factory;
use Psr\Container\ContainerInterface;

use function defined;

class HopperComponent extends MVCComponent implements BootableExtensionInterface
{
    protected static $dic;

    public function boot(ContainerInterface $container)
    {
        self::$dic = $container;
    }

    public static function getContainer()
    {
        if (empty(self::$dic)) {
            Factory::getApplication()->bootComponent('com_hopper');
        }

        return self::$dic;
    }
}