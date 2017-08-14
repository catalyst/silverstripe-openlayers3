<?php

/**
 * This file contains the "OL3MapTestTest" class.
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

/**
 * Testing the ColorFieldTest class.
 */

class OL3MapTest extends SapphireTest
{

    /**
     * @return void
     */
    public function testValidate()
    {
        $map = OL3Map::create();
        $validator = RequiredFields::create();
        
        // No value
        $map->MinZoom = '';
        $map->Zoom = '';
        $result = $map->validate($validator);
        $this->assertInternalType('boolean', $result);
        $this->assertTrue($result);
        
        $map->MaxZoom = '';
        $map->Zoom = '';
        $result = $map->validate($validator);
        $this->assertInternalType('boolean', $result);
        $this->assertTrue($result);
        
        $map->Zoom = '';
        $map->MinZoom = '';
        $map->MaxZoom = '';
        $result = $map->validate($validator);
        $this->assertInternalType('boolean', $result);
        $this->assertTrue($result);
        
        // Invalid values
        $map->setField('MaxZoom', 99);
        $map->setField('Zoom', 100);
        $result = $map->validate($validator);
        $this->assertInstanceOf('ValidationResult', $result);
        $this->assertFalse($result->valid());
        
        $map->setField('MinZoom', 100);
        $map->setField('Zoom', 99);
        $result = $map->validate($validator);
        $this->assertInstanceOf('ValidationResult', $result);
        $this->assertFalse($result->valid());
        
        // Valid views
        $map->setField('MinZoom', 98);
        $map->setField('MaxZoom', 100);
        $map->setField('Zoom', 99);
        $result = $map->validate($validator);
        $this->assertInstanceOf('ValidationResult', $result);
        $this->assertTrue($result->valid());
    }
}
