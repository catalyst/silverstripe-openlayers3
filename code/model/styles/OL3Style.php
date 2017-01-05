
<?php
class OL3Style extends DataObject
{
    private static $singular_name = 'Style';
    private static $plural_name = 'Styles';

    private static $summary_fields = [
        'Title' => 'Title',
        'singular_name' => 'Type',
    ];

    public function getTitle()
    {
        return 'new Style';
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        foreach ($this->config()->get('has_one') ?: [] as $componentName => $className) {

            if (!is_a($className, 'OL3Style', true)) continue;

            $fields->addFieldToTab('Root.Main', HeaderField::create($componentName));

            if ($this->has_one($componentName)) $component = $this->$componentName() ?: Object::create($className);

            foreach ($component->getCMSFields()->dataFields() as $fieldName => $field) {

                $name = $componentName . '_' . $field->getName();
                $title = $componentName . ' ' . $field->Title();

                if (strpos($fieldName, '_') === false) {

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

    public function getStyles(&$styles)
    {
        if ($this->exists()) {
            $styles[$this->ID] = $this->toMap();

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

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $data = Controller::curr()->getRequest()->postVars();
        $components = [];
        foreach ($this->record as $key => $val) {
            $segments = explode('_', $key);
            $componentName = array_shift($segments);
            if (count($segments)) {
                if (empty($components[$componentName])) {
                    if ($this->has_one($componentName)) {
                        $components[$componentName] = $this->$componentName() ?: Object::create($componentName);
                    } else {
                        // class-has-changed situation
                        continue;
                    }
                }
                $componentPropertyName = implode('_', $segments);

                if ($componentPropertyName == 'ClassName' && $components[$componentName]->ClassName != $val) {
                    $components[$componentName] = $components[$componentName]->newClassInstance($val);
                } else {
                    $components[$componentName]->$componentPropertyName = $val;
                }
            }
        }
        foreach ($components as $componentName => $component) {
            $this->setField($componentName . 'ID', $component->write());
        }
    }
}
