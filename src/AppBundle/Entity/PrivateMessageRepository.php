<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * PrivateMessageRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class PrivateMessageRepository extends EntityRepository
{
    /**
     * @param User $recipient
     * @return mixed
     */
    public function getUnreadPrivateMessagesCount(User $recipient)
    {
        return $this->createQueryBuilder('pm')
            ->select('count(pm.id)')
            ->innerJoin('pm.recipient', 'r', Join::WITH, 'r = :recipient')
            ->where('pm.isViewed = :isViewed')
            ->setParameters(['recipient' => $recipient, 'isViewed' => false])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param User $recipient
     * @return mixed
     */
    public function getAllReceivedPrivateMessages(User $recipient)
    {
        return $this->createQueryBuilder('pm')
            ->select('pm, s')
            ->innerJoin('pm.recipient', 'r', Join::WITH, 'r = :recipient')
            ->join('pm.sender', 's')
            ->where('pm.deletedFromReceived = :deletedFromReceived')
            ->orderBy('pm.sentTime', 'DESC')
            ->setParameters(['recipient' => $recipient, 'deletedFromReceived' => false])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $sender
     * @return mixed
     */
    public function getAllSentPrivateMessages(User $sender)
    {
        return $this->createQueryBuilder('pm')
            ->select('pm, r')
            ->innerJoin('pm.sender', 's', Join::WITH, 's = :sender')
            ->join('pm.recipient', 'r')
            ->where('pm.deletedFromSent = :deletedFromSent')
            ->orderBy('pm.sentTime', 'DESC')
            ->setParameters(['sender' => $sender, 'deletedFromSent' => false])
            ->getQuery()
            ->getResult();
    }
}
