<?php

use PHPUnit\Framework\TestCase;

use InterviewCalendar\Model\Availability;
use InterviewCalendar\ValueObject\Interval;
use InterviewCalendar\ValueObject\DateInFuture;

class AvailabilityTest extends TestCase
{
    
    public function setUp(): void
    {
        $this->date = new DateInFuture(
            Date('Y-m-d', time() + 1 * 24 * 3600)
        );
        
        $this->interval1 = new Interval(10, 14);
        $this->interval2 = new Interval(15, 19);
        $this->interval3 = new Interval(9, 14);

    }

    public function testAvailabilityCreation()
    {
        $availability = new Availability(
            $this->date,
            [
                $this->interval1,
                $this->interval2
            ]
        );

        $this->assertEquals($availability->date(), (string) $this->date);
        $this->assertEquals(
            $availability->intervals(), 
            [
                $this->interval1,
                $this->interval2
            ]
        );

        $this->assertEquals(
            $availability->range(),
            [
                'date' => (string) $this->date,
                'range' => [
                    10, 11, 12, 13, 15, 16, 17, 18
                ]
            ]
        );

        $this->assertEquals(
            $availability->toArray(),
            [
                'date' => (string) $this->date,
                'intervals' => [
                    $this->interval1,
                    $this->interval2
                ]
            ]
        );

        $this->assertEquals(
            $availability->jsonSerialize(),
            [
                'date' => (string) $this->date,
                'intervals' => [
                    $this->interval1,
                    $this->interval2
                ]
            ]
        );

    }


    public function testAddInterval()
    {
        $availability = new Availability(
            $this->date,
            [
                $this->interval1,
                $this->interval2
            ]
        ); 

        $availability->addInterval($this->interval3);

        $this->assertCount(3, $availability->intervals());
        
        $this->assertEquals(
            $availability->range(),
            [
                'date' => (string) $this->date,
                'range' => [
                    9, 10, 11, 12, 13, 15, 16, 17, 18
                ]
            ]
        );

    }

}