<?php

namespace App\EventListener;

use App\Entity\User;
use App\Helper\Interfaces\HistoryInterface;
use App\Service\HistoryService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HistorySubscriber implements EventSubscriber
{
    public function __construct(
        protected TokenStorageInterface $tokenStorage,
        protected HistoryService        $historyService
    )
    {
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postUpdate,
        ];
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if(!($entity instanceof HistoryInterface)){
            return;
        }
        if ($this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();
        } else {
            $user = null;
        }
        if ($user instanceof User) {
            $this->historyService->recordHistory($entity, $user);
        }
    }
}