<?php

/**
 * File told conatain OL3Style
 *
 * @package openlayers3
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 */

/**
 * A base class for ol.styles
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.style.html
 */

class OL3Style extends DataObject
{
    /**
     * Some style classes have styles as their components. The nesting is non recursive and it's depth is finite.
     * This structure represents the structure of Openlayers3 ol.style structure.
     *
     * Since most styles contain little data and there are so many of them, editing each individual style in it's own
     * form would be tedious. So style nodes pull all fields from their child nodes into CMSFields which results
     * in a flat form structure
     *
     * @return Object FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // loop over style components
        foreach ($this->config()->get('has_one') ?: [] as $componentName => $className) {

            // only process styles
            if (!is_a($className, 'OL3Style', true)) continue;

            // add a header field for each child style node
            $fields->addFieldToTab('Root.Main', HeaderField::create($componentName));

            // get child style node instance or create one if necessary
            if ($this->has_one($componentName)) $component = $this->$componentName() ?: Object::create($className);

            // collect all fields of the instance and add them to CMSFields
            foreach ($component->getCMSFields()->dataFields() as $fieldName => $field) {

                // use componentName_fieldName syntax to avoid field name conflicts
                $name = $componentName . '_' . $field->getName();
                $title = $componentName . ' ' . $field->Title();

                // set the value only in the first iteration since it will not be available in the next loop hence be null
                if (strpos($fieldName, '_') === false) {

                    // UploadField edge case: UploadField::setValue() is inconsistent with the way data is being
                    // loaded into the flattened form fields
                    if (is_a($component->has_one($fieldName), 'File', true) && $field instanceof UploadField) {
                        $field->setValue(null, $component);
                    } else {
                        $field->setValue($component->$fieldName);
                    }
                }

                $field->setName($name)->setTitle($title);

                $fields->addFieldToTab('Root.Main', $field);
            }

            $fields->removeByName($componentName . 'ID');
        }

        return $fields;
    }

    /**
     * Method to collect styles
     * Each style adds itself to the provided $style array reference, it's ID being the $style arrays key
     * and it's protected record property array the value. If the style has style components itself, the
     * calls getStyles on them too.
     * @param &$styles Array to which the styles get added
     * @return void
     * @see OL3Style::getCMSFields()
     * @see OL3Map::JsonStyles()
     */
    public function getStyles(&$styles)
    {
        // only makes sense if the style exists
        if ($this->exists()) {
            // add $this to the styles array refence
            $styles[$this->ID] = $this->toMap();

            // loop over style components
            $components = $this->config()->get('has_one');
            if ($components) foreach($components as $componentName => $componentClass) {
                if (is_a($componentClass, 'OL3Style', true) && $curr = $this->$componentName()) {
                    // traverse deeper into the nested style structure
                    $curr->getStyles($styles);
                } else if (is_a($componentClass, 'File', true) && $curr = $this->$componentName()) {
                    // add Filename for file components
                    $styles[$this->ID][$componentName . 'SRC'] = $curr->Filename;
                }
            }
        }
    }

    /**
     * Make sure that the CMSFields vales get written to the styles components.
     * @return void
     * @see OL3Style::getCMSFields()
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // components cache
        $components = [];

        // loop over properties
        foreach ($this->record as $key => $val) {

            // split property names by underscore and take the first name as the child style node comontents name
            $segments = explode('_', $key);
            $componentName = array_shift($segments);

            // if no unconsumed segements are left, this is not a component
            if (count($segments)) {

                // warm up components cache
                if (empty($components[$componentName])) {
                    if ($this->has_one($componentName)) {
                        // get component or create it if necessary
                        $components[$componentName] = $this->$componentName() ?: Object::create($componentName);
                    } else {
                        // class-has-changed situation
                        continue;
                    }
                }
                // concatenate remaning segments to be the data for recursive calls
                $componentPropertyName = implode('_', $segments);

                // set the value of the comontent
                // if the class has been changed, don't set the value but create a new instance of the new class
                if ($componentPropertyName == 'ClassName' && $components[$componentName]->ClassName != $val) {
                    $components[$componentName] = $components[$componentName]->newClassInstance($val);
                } else {
                    $components[$componentName]->$componentPropertyName = $val;
                }
            }
        }

        // write all components to db
        foreach ($components as $componentName => $component) {
            $this->setField($componentName . 'ID', $component->write());
        }
    }
}
