<?php

namespace App\Events;

use App\Entity\Customer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HashUserPasswordSubscriber implements EventSubscriberInterface
{

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['createUser', EventPriorities::PRE_WRITE]
        ];
    }


    public function createUser(ViewEvent $event)
    {
        $result = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        /**
         * On cherche à savoir si la requête correspond
         * à la mise à jour d'un mot de passe
         */
        $endpoint = null;
        $uri = explode("/", $event->getRequest()->getUri());
        if (array_key_exists(6, $uri)) $endpoint = $uri[6];

        if ($result instanceof Customer) {
            if ($method === Request::METHOD_POST) {
                /** @var Customer $result */
                $pwd_hashed =
                    $endpoint === "update-password" ?
                    $this->hasher->hashPassword($result, $result->getNewPassword())
                    : $this->hasher->hashPassword($result, $result->getPassword());
                $result->setPassword($pwd_hashed);
            }
        }
    }
}
