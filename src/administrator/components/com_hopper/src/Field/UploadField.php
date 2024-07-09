<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Obix\Component\Hopper\Administrator\Field;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\WebAsset\WebAssetManager;
use SimpleXMLElement;

class UploadField extends FormField
{
    /**
     * The form field type.
     *
     * @var    string
     */
    protected $type = 'Upload';

    private string $maxUploadSize = '2M';

    private string $destDir = JPATH_ROOT . '/tmp';

    private string $files = '';

    /**
     * Name of the layout being used to render the field
     *
     * @var    string
     */
    protected $layout = 'form.field.upload';

    public function __construct($form = null)
    {
        parent::__construct($form);

        $doc = Factory::getApplication()->getDocument();

        /** @var WebAssetManager $wa */
        $wa = $doc->getWebAssetManager();
        if (!$wa->assetExists('preset', 'upload')) {
            $wr = $wa->getRegistry();
            $wr->addRegistryFile('media/com_hopper/joomla.asset.json');
        }
        $wa->usePreset('upload');
    }

    /**
     * Method to attach a JForm object to the field.
     *
     * @param SimpleXMLElement $element The SimpleXMLElement object representing the `<field>` tag for the form field object.
     * @param mixed $value The form field value to validate.
     * @param string $group The field name group control value. This acts as an array container for the field.
     *                                      For example if the field has name="foo" and the group value is set to "bar" then the
     *                                      full field name would end up being "bar[foo]".
     *
     * @return  boolean  True on success.
     *
     * @see     FormField::setup()
     */
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        if (($result = parent::setup($element, $value, $group)) !== true) {
            return $result;
        }

        $this->files = $value;

        return $result;
    }

    /**
     * Method to get certain otherwise inaccessible properties from the form field object.
     *
     * @param string $name The property name for which to get the value.
     *
     * @return  mixed  The property value or null.
     */
    public function __get($name)
    {
        switch ($name) {
            default:
                return parent::__get($name);
        }
    }

    /**
     * Method to set certain otherwise inaccessible properties of the form field object.
     *
     * @param string $name The property name for which to set the value.
     * @param mixed $value The value of the property.
     *
     * @return  void
     */
    public function __set($name, $value)
    {
        switch ($name) {
            default:
                parent::__set($name, $value);
        }
    }

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        return $this->getRenderer($this->layout)->render($this->getLayoutData());
    }

    /**
     * Method to get the data to be passed to the layout for rendering.
     *
     * @return  array
     */
    protected function getLayoutData()
    {
        $data = parent::getLayoutData();

        $filesInputId = $data['id'] . '-files';
        $filesInputName = $this->getName($this->fieldname . '-files');

        $doc = Factory::getApplication()->getDocument();
        $doc->addScriptOptions('obixUploadField', [
            'fields' => [
                $filesInputId => $filesInputName
            ]
        ], true);

        $extraData = array(
            'filesId' => $filesInputId,
            'filesName' => $filesInputName,
            'value' => $this->value,
        );

        return array_merge($data, $extraData);
    }

    public function getProperty(string $name): ?string
    {
        return $this?->$name ?? null;
    }
}
