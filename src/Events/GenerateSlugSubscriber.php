<?php

namespace App\Events;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Subcategory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\String\Slugger\SluggerInterface;

class GenerateSlugSubscriber implements EventSubscriberInterface
{

    public function __construct(private SluggerInterface $slugger)
    {
    }
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['generateSlug', EventPriorities::PRE_VALIDATE]
        ];
    }

    public function generateSlug(ViewEvent $event): void
    {
        $result = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($result instanceof Category || $result instanceof Subcategory || $result instanceof Product) {
            if ($method === Request::METHOD_POST || $method === Request::METHOD_PATCH) {
                $result->setSlug($this->slugGeneration($result->getName()));
            }
        }
    }

    private function slugGeneration(String $string): string
    {
        return strtolower($this->slugger->slug($string));
    }
}
