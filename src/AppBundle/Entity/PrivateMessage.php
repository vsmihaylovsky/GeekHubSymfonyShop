<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 3/3/16
 * Time: 8:55 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="private_message")
 */
class PrivateMessage
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     * @ORM\JoinColumn(name="recipient_id", referencedColumnName="id")
     */
    private $recipient;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $sentTime;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(max = 100)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=1000)
     * @Assert\NotBlank()
     * @Assert\Length(max = 1000)
     */
    private $message;


    /**
     * @ORM\Column(type="boolean")
     */
    private $isViewed;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deletedFromSent;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deletedFromReceived;


    public function __construct()
    {
        $this->setDeletedFromSent(false);
        $this->setDeletedFromReceived(false);
        $this->setIsViewed(false);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sentTime
     *
     * @param \DateTime $sentTime
     * @return PrivateMessage
     */
    public function setSentTime($sentTime)
    {
        $this->sentTime = $sentTime;

        return $this;
    }

    /**
     * Get sentTime
     *
     * @return \DateTime 
     */
    public function getSentTime()
    {
        return $this->sentTime;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return PrivateMessage
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return PrivateMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set isViewed
     *
     * @param boolean $isViewed
     * @return PrivateMessage
     */
    public function setIsViewed($isViewed)
    {
        $this->isViewed = $isViewed;

        return $this;
    }

    /**
     * Get isViewed
     *
     * @return boolean 
     */
    public function getIsViewed()
    {
        return $this->isViewed;
    }

    /**
     * Set deletedFromSent
     *
     * @param boolean $deletedFromSent
     * @return PrivateMessage
     */
    public function setDeletedFromSent($deletedFromSent)
    {
        $this->deletedFromSent = $deletedFromSent;

        return $this;
    }

    /**
     * Get deletedFromSent
     *
     * @return boolean 
     */
    public function getDeletedFromSent()
    {
        return $this->deletedFromSent;
    }

    /**
     * Set deletedFromReceived
     *
     * @param boolean $deletedFromReceived
     * @return PrivateMessage
     */
    public function setDeletedFromReceived($deletedFromReceived)
    {
        $this->deletedFromReceived = $deletedFromReceived;

        return $this;
    }

    /**
     * Get deletedFromReceived
     *
     * @return boolean 
     */
    public function getDeletedFromReceived()
    {
        return $this->deletedFromReceived;
    }

    /**
     * Set sender
     *
     * @param \AppBundle\Entity\User $sender
     * @return PrivateMessage
     */
    public function setSender(\AppBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \AppBundle\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set recipient
     *
     * @param \AppBundle\Entity\User $recipient
     * @return PrivateMessage
     */
    public function setRecipient(\AppBundle\Entity\User $recipient = null)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get recipient
     *
     * @return \AppBundle\Entity\User 
     */
    public function getRecipient()
    {
        return $this->recipient;
    }
}
