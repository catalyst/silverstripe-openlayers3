
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

            if (substr($className, -5) != 'Style') continue;

            $fields->addFieldToTab('Root.Main', HeaderField::create($componentName));

            $component = $this->$componentName() ?: Object::create($className);

            foreach ($component->getCMSFields()->dataFields() as $fieldName => $field) {

                $name = $componentName . '_' . $field->getName();
                $title = $componentName . ' ' . $field->Title();
                $field->setName($name)->setTitle($title);


                if (strpos($fieldName, '_') === false) {
                    $field->setValue($component->$fieldName);
                }
                $fields->addFieldToTab('Root.Main', $field);
            }

            $fields->removeByName($componentName . 'ID');
        }

        return $fields;
    }

    public function getStyles(&$styles)
    {
        if ($this->exists()) $styles[$this->ID] = $this->toMap();
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
                    $components[$componentName] = $this->$componentName() ?: Object::create($componentName);
                }
                $componentPropertyName = implode('_', $segments);

                $components[$componentName]->$componentPropertyName = $val;
            }
        }

        foreach ($components as $componentName => $component) {
            $this->setField($componentName . 'ID', $component->write());
        }
    }
}
