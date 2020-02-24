<?php

namespace VoteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Vote
 *
 * @ORM\Table(name="pn_vote")
 * @ORM\Entity(repositoryClass="VoteBundle\Repository\VoteRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Vote
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="total_votes", type="integer")
     */
    protected $totalVotes = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="resource_id", type="integer", unique=true)
     */
    protected $resourceId;

    /**
     * @var int
     *
     * @ORM\Column(name="positive", type="integer")
     */
    protected $positive;

    /**
     * @var int
     *
     * @ORM\Column(name="negative", type="integer")
     */
    protected $negative;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_created", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="date_modified", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateModified;

    public function __construct()
    {
        $this->dateCreated = new \DateTime();
        $this->dateModified = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getTotalVotes(): int
    {
        return $this->totalVotes;
    }

    /**
     * @param int $totalVotes
     */
    public function setTotalVotes(int $totalVotes): void
    {
        $this->totalVotes = $totalVotes;
    }

    /**
     * @return int
     */
    public function getResourceId(): int
    {
        return $this->resourceId;
    }

    /**
     * @param int $resourceId
     */
    public function setResourceId(int $resourceId): void
    {
        $this->resourceId = $resourceId;
    }

    /**
     * @return int
     */
    public function getPositive(): int
    {
        return $this->positive;
    }

    /**
     * @param int $positive
     */
    public function setPositive(int $positive): void
    {
        $this->positive = $positive;
    }

    /**
     * @return int
     */
    public function getNegative(): int
    {
        return $this->negative;
    }

    /**
     * @param int $negative
     */
    public function setNegative(int $negative): void
    {
        $this->negative = $negative;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated(): \DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     */
    public function setDateCreated(\DateTime $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDateModified(): \DateTime
    {
        return $this->dateModified;
    }

    /**
     * @param \DateTime $dateModified
     */
    public function setDateModified(\DateTime $dateModified): void
    {
        $this->dateModified = $dateModified;
    }
}