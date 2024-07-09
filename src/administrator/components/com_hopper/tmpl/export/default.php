<?php
/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\WebAsset\WebAssetManager;

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa
    ->useStyle('com_hopper.admin');

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$formAction = Route::_('index.php?option=com_hopper&task=export.export');
?>
<form action="<?= $formAction ?>" method="post" name="adminForm" id="export-form" class="form-validate">
    <div>
        <div class="row">
            <div class="col-md-9">
                <div class="version-wrapper">
                    <?php
                    echo $this->form->renderField('version'); ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <?= HTMLHelper::_('form.token'); ?>
</form>