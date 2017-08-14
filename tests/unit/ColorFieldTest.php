<?php

/**
 * This file contains the "ColorFieldTest" class.
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

/**
 * Testing the ColorFieldTest class.
 */

class ColorFieldTest extends SapphireTest
{

    /**
     * @return void
     */
    public function testValidate()
    {
        $field = ColorField::create('Color');
        $validator = RequiredFields::create();
        
        // No value
        $field->setValue('');
        $this->assertTrue($field->validate($validator));
        
        // Valid regex formats
        $field->setValue('rgba( 1 , 2 , 3 , 4 )');
        $this->assertTrue($field->validate($validator));
        $field->setValue('rgba(1,2,3,4)');
        $this->assertTrue($field->validate($validator));
        $field->setValue('rgba(255,255,0,.25)');
        $this->assertTrue($field->validate($validator));
        
        // Invalid regex format
        $field->setValue('rgba( 1 , 2 , 3 , )');
        $this->assertFalse($field->validate($validator));
    }
}
