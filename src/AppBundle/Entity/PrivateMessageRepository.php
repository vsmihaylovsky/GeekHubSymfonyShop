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
            ->where('pm.isViewed = :isViewed and pm.deletedFromReceived = :deletedFromReceived')
            ->setParameters(['recipient' => $recipient, 'isViewed' => false, 'deletedFromReceived' => false])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param User $recipient
     * @return mixed
     */
    public function getAllReceivedPrivateMessages(User $recipient)
    {
        return $this->_em->createQueryBuilder()
            ->select('pm, s')
            ->from($this->_entityName, 'pm', 'pm.id')
            ->innerJoin('pm.recipient', 'r', Join::WITH, 'r = :recipient')
            ->innerJoin('pm.sender', 's')
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
        return $this->_em->createQueryBuilder()
            ->select('pm, r')
            ->from($this->_entityName, 'pm', 'pm.id')
            ->innerJoin('pm.sender', 's', Join::WITH, 's = :sender')
            ->innerJoin('pm.recipient', 'r')
            ->where('pm.deletedFromSent = :deletedFromSent')
            ->orderBy('pm.sentTime', 'DESC')
            ->setParameters(['sender' => $sender, 'deletedFromSent' => false])
            ->getQuery()
            ->getResult();
    }
}
