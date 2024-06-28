<?php
/**
 * @package     com_handover
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\WebAsset\WebAssetManager;

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa
    ->useStyle('com_handover.admin');
?>

<div class="container">
    <div class="row">
        <div class="col col-12 col-lg-8">
            <a class="btn btn-outline-primary border-0" style="width: 10em;"
               href="index.php?option=com_handover&view=export&<?= Factory::getApplication()->getFormToken() ?>=1">
                <div class="bg-primary text-white d-block text-center p-3 h2 border-radius">
                    <span class="fa fa-file-export"></span>
                </div>
                <span><?= Text::_('COM_HANDOVER_CPANEL_BUTTON_EXPORT_LABEL') ?></span>
            </a>
            <a class="btn btn-outline-primary border-0" style="width: 10em;"
               href="index.php?option=com_handover&view=import&<?= Factory::getApplication()->getFormToken() ?>=1">
                <div class="bg-primary text-white d-block text-center p-3 h2 border-radius">
                    <span class="fa fa-file-import"></span>
                </div>
                <span><?= Text::_('COM_HANDOVER_CPANEL_BUTTON_IMPORT_LABEL') ?></span>
            </a>
        </div>

        <div class="col-12 col-lg-4">
        </div>
    </div>
</div>
