<?php

use PHPUnit\Framework\TestCase;

use InterviewCalendar\ValueObject\Exception;
use Ramsey\Uuid\Uuid as RamseyUuid;

use InterviewCalendar\ValueObject\Interval;

class IntervalTest extends TestCase
{
    /**
     * @dataProvider validIntervalsProvider
     */
    public function testValidIntervalGeneration($start, $end, $step, $expectedArray, $expectedString, $expectedSerialization)
    {
        $interval = new Interval($start, $end, $step);

        $this->assertEquals($interval->start(), $start);
        $this->assertEquals($interval->end(), $end);

        $this->assertEquals($interval->range(), $expectedArray);
        $this->assertEquals((string) $interval, $expectedString);
        $this->assertEquals($interval->jsonSerialize(), $expectedSerialization);
    }

    public function validIntervalsProvider()
    {
        return [
            [
                5, 10, null, 
                [5, 6, 7, 8, 9], 
                '[5,10)',
                '5 - 10'
            ],
            [
                0, 2, 1, 
                [0, 1], 
                '[0,2)',
                '0 - 2'
            ],
            [
                10, 18, null, 
                [10, 11, 12, 13, 14, 15, 16, 17],
                '[10,18)',
                '10 - 18'
            ]            
            
        ];
    }

    /**
     * @dataProvider invalidIntervalsProvider
     */
    public function testInvalidIntervals($start, $end, $step, $expected)
    {
        $this->expectException($expected);
        $interval = new Interval($start, $end, $step);
    }

    public function invalidIntervalsProvider()
    {
        return [
            [
                -5, 10, null, 
                Exception\InvalidIntervalBorder::class
            ],
            [
                25, 10, null, 
                Exception\InvalidIntervalBorder::class
            ],
            [
                1, -4, null, 
                Exception\InvalidIntervalBorder::class
            ],   
            [
                1, 25, null, 
                Exception\InvalidIntervalBorder::class
            ],
            [
                1, 1, null, 
                Exception\InvalidIntervalBorder::class
            ],
            [
                1, 3, 4, 
                Exception\InvalidIntervalStep::class
            ]                   
            
        ];
    }

    public function testIntersection()
    {
        $interval1 = new Interval(10, 19);
        $interval2 = new Interval(12, 18);
        
        $this->assertEquals(
            array_values($interval1->intersect($interval2)),
            [12, 13, 14, 15, 16, 17]
        );

    }

    
}