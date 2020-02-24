<?php

namespace VoteBundle\Repository;

use VoteBundle\Entity\Vote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class VoteRepository
 * @package VoteBundle\Repository
 */
class VoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    /**
     * @param Vote|null $voteEntity
     * @param int $resourceId
     * @param bool $positiveVote
     * @return Vote
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addVote(Vote $voteEntity = null, int $resourceId, bool $positiveVote)
    {
        if ($voteEntity) {
            return $this->updateVote($voteEntity, $positiveVote);
        }

        return $this->createVote($resourceId, $positiveVote);
    }

    /**
     * @param Vote $voteEntity
     * @param bool $positiveVote
     * @return Vote
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function updateVote(Vote $voteEntity, bool $positiveVote)
    {
        if ($positiveVote) {
            $voteEntity->setPositive($voteEntity->getPositive() + 1);
        } else {
            $voteEntity->setNegative($voteEntity->getNegative() + 1);
        }
        $voteEntity->setTotalVotes($voteEntity->getTotalVotes() + 1);
        $this->getEntityManager()->persist($voteEntity);
        $this->getEntityManager()->flush($voteEntity);
        return $voteEntity;
    }

    /**
     * @param int $resourceId
     * @param bool $positiveVote
     * @return Vote
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function createVote(int $resourceId, bool $positiveVote)
    {
        $vote = new Vote();
        if ($positiveVote) {
            $vote->setPositive(1);
            $vote->setNegative(0);
        } else {
            $vote->setNegative(1);
            $vote->setPositive(0);
        }
        $vote->setTotalVotes(1);
        $vote->setResourceId($resourceId);
        $this->getEntityManager()->persist($vote);
        $this->getEntityManager()->flush($vote);
        return $vote;
    }
}