<?php

namespace App\Event;

class NbpApiCallEvent extends \Symfony\Contracts\EventDispatcher\Event
{
    public \DateTime $startTime;
    public \DateInterval $diff;
    public function __construct()
    {
        $this->startTime = new \DateTime('now');
    }

}