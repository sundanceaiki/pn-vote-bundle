<?php declare(strict_types=1);

namespace VoteBundle\EventSubscriber;

use VoteBundle\Entity\Vote;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class VoteSubscriber implements EventSubscriber
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * VoteSubscriber constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate'
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        if ($args->getEntity() instanceof Vote) {
            $this->assignVoter($args->getEntity());
        }
    }

    /**
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PreUpdateEventArgs $event): void
    {
        if ($event->getEntity() instanceof Vote) {
            $this->assignVoter($event->getEntity());
        }
    }

    /**
     * @param Vote $vote
     */
    private function assignVoter(Vote $vote): void
    {
        if (null !== ($token = $this->tokenStorage->getToken())) {
            if ($token->getUser() instanceof UserInterface) {
                $vote->setVoter($token->getUser());
            }
        }
    }
}