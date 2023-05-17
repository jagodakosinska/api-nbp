<?php

namespace App\Subscriber;

use App\Event\NbpApiCallEvent;

class NbpApiCallSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [NbpApiCallEvent::class => 'onNbpApiCall'];
    }

    public function onNbpApiCall(NbpApiCallEvent $event)
    {
        $now = new \DateTime();
        $event->diff = $now->diff($event->startTime);
        dump($event->diff);
    }
}