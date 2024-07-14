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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));


$action = Route::_('index.php?option=com_hopper&view=releases');
?>

<form action="<?= $action ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <?= LayoutHelper::render('joomla.searchtools.default', ['view' => $this]) ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <caption><?= Text::_('COM_HOPPER_RELEASES_LIST_TITLE') ?></caption>
            <thead>
            <tr>
                <td><?= Text::_('COM_HOPPER_RELEASES_LIST_TITLE_HEADER') ?></td>
                <td><?= Text::_('COM_HOPPER_RELEASES_LIST_VERSION_HEADER') ?></td>
                <td><?= Text::_('COM_HOPPER_RELEASES_LIST_ID_HEADER') ?></td>
            </tr>
            </thead>
            <tbody>

            <?php
            foreach ($this->items as $item) : ?>
                <tr>
                    <td><?= $item->title ?></td>
                    <td><?= $item->version ?></td>
                    <td><?= $item->id ?></td>
                </tr>
            <?php
            endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= $this->pagination->getListFooter() ?>

    <input type="hidden" name="task" value="">
    <?= HTMLHelper::_('form.token') ?>
</form>
