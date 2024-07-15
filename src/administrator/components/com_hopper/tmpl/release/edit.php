<?php

/**
 * @package     com_hopper
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$action = Route::_('index.php?option=com_hopper&view=project&layout=edit&id=' . (int)$this->item->id);
?>

<form action="<?= $action ?>" method="post" name="adminForm" id="project-form" class="form-validate">
    <div>
        <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <?= $this->form->renderField('id') ?>
                        <?= $this->form->renderField('project_id') ?>
                        <?= $this->form->renderField('version') ?>
                        <?= $this->form->renderField('note') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <?= HTMLHelper::_('form.token') ?>
</form>
