<?php

/**
 * This file contains the "OL3LayerTest" class.
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

/**
 * Testing the OL3Layer class
 */

class OL3LayerTest extends SapphireTest
{
    /**
     * SapphireTest will not call requireDefaultRecords, we need to manually tell
     * it, on which which classes to call it.
     *
     * An array of {@link Page} subclasses that are pre-built vy calls made to
     * requireDefaultRecords().
     *
     * @return array
     */
     protected $requireDefaultRecordsFrom = [
         'OL3Layer',
     ];

    /**
     * @var string
     */
    // protected static $fixture_file = 'fixtures/OL3LayerTest.yml';

    public function testTest()
    {
        $this->assertTrue(false);
    }
}
