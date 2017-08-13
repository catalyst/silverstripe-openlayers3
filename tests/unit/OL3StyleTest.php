<?php

/**
 * This file contains the "OL3StyleTest" class.
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

/**
 * Testing the OL3Style class
 */

class OL3StyleTest extends SapphireTest
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
         'OL3Style',
     ];

    /**
     * @var string
     */
    // protected static $fixture_file = 'fixtures/OL3StyleTest.yml';

    public function testTest()
    {
        $this->assertTrue(false);
    }
}
