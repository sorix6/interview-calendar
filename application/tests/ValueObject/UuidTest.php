<?php

use PHPUnit\Framework\TestCase;

use InterviewCalendar\ValueObject\Exception\InvalidUuid;
use Ramsey\Uuid\Uuid as RamseyUuid;

use InterviewCalendar\ValueObject\Uuid;

class UuidTest extends TestCase
{
    /**
     * @dataProvider uuidValidStringsProvider
     */
    public function testUuidFromValidString($uuidString, $expected)
    {
        $uuid = new Uuid($uuidString);

        $this->assertEquals((string) $uuid, (string) $expected);
        
    }

    public function uuidValidStringsProvider()
    {
        // test UUID v1, v4 and random strings
        return [
            [
                '7112b944-d668-4137-9b30-4688e7b905f1', RamseyUuid::fromString('7112b944-d668-4137-9b30-4688e7b905f1') 
            ],
            [
                '262d4940-e083-45cb-901c-d3cb81085379', RamseyUuid::fromString('262d4940-e083-45cb-901c-d3cb81085379') 
            ]           
            
        ];
    }

    public function testUuidFromEmptyInput()
    {
        $uuid = new Uuid();

        $this->assertTrue(RamseyUuid::isValid((string) $uuid));
        $this->assertEquals(4, RamseyUuid::fromString((string) $uuid)->getVersion());
        
    }

    /**
     * @dataProvider uuidInvalidStringsProvider
     */
    public function testUuidFromInvalidString($uuidString, $expected)
    {
        $this->expectException($expected);
        $uuid = new Uuid($uuidString);
    }


    public function uuidInvalidStringsProvider()
    {
        // test UUID v1, v4 and random strings
        return [
            [
                '00c9921e-c12e-11e9-9cb5-2a2ae2dbcce4', InvalidUuid::class
            ],
            [
                '00c99534-c12e-11e9-9cb5-2a2ae2dbcce4', InvalidUuid::class
            ],
            [
                'this-is-not-a-valid-uuid', InvalidUuid::class 
            ],
            [
                'thisIsNotAValidUUID', InvalidUuid::class 
            ],
            
            
        ];
    }
}