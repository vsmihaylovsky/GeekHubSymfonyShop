<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 3/10/16
 * Time: 1:24 PM
 */

namespace AppBundle\Security;

use AppBundle\Entity\PrivateMessage;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class PrivateMessageVoter extends Voter
{
    const READ_MESSAGE = 'read_message';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::READ_MESSAGE))) {
            return false;
        }

        // only vote on User objects inside this voter
        if (!$subject instanceof PrivateMessage) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var PrivateMessage $privateMessage */
        $privateMessage = $subject;
        switch($attribute) {
            case self::READ_MESSAGE:
                return $this->canReadMessage($privateMessage, $user);
        }
        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param PrivateMessage $privateMessage
     * @param User $user
     * @return bool
     */
    private function canReadMessage(PrivateMessage $privateMessage, User $user)
    {
        return ($privateMessage->getSender() === $user) || ($privateMessage->getRecipient() === $user);
    }
}