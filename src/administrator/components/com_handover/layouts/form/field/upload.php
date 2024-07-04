<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   (C) 2016 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * https://css-tricks.com/drag-and-drop-file-uploading/
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/** @var array $displayData */
$id = $displayData['id'];
$name = $displayData['name'];
$filesId = $displayData['filesId'];
$filesName = $displayData['filesName'] . '[]';
$value = htmlspecialchars($displayData['value']);
?>

<div class="upload-box">
    <div class="upload-box__icon"><span class="fa fa-file-upload"></span></div>
    <div class="upload-box__input">
        <input type="hidden" name="<?= $name ?>" id="<?= $id ?>" value="<?= $value ?>"/>
        <input class="upload-box__file" type="file" name="<?= $filesName ?>" id="<?= $filesId ?>"
               data-multiple-caption="<?= Text::_('COM_HANDOVER_IMPORT_FORM_FIELD_FILES_NUM_FILES_ADDED') ?>" multiple/>
        <label for="<?= $filesId ?>"><strong>Choose a file</strong><span
                    class="upload-box__dragndrop"> or drag it here</span>.</label>
        <button class="upload-box__button" type="submit">Upload</button>
    </div>
    <div class="upload-box__uploading">Uploading…</div>
    <div class="upload-box__success">Done!</div>
    <div class="upload-box__error">Error! <span></span>.</div>
</div>
