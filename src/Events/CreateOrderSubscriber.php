<?php

namespace App\Events;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CreateOrderSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setOrderDefault', EventPriorities::PRE_VALIDATE]
        ];
    }

    public function setOrderDefault(ViewEvent $event): void
    {
        $result = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($result instanceof Order && $method === Request::METHOD_POST) {
            /** @var Order $result */
            $result
                ->setReference(strtoupper(uniqid()))
                ->setStatus($result::STATE[0]);
        }

        return;
    }
}
