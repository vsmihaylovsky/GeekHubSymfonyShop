<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class UserVoter extends Voter
{
    const SELF_EDIT = 'self_edit';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::SELF_EDIT))) {
            return false;
        }

        // only vote on User objects inside this voter
        if (!$subject instanceof User) {
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
        /** @var User $checkedUser */
        $checkedUser = $subject;
        switch($attribute) {
            case self::SELF_EDIT:
                return $this->canSelfEdit($checkedUser);
        }
        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param User $user
     * @return bool
     */
    private function canSelfEdit(User $user)
    {
        if (($user->getFacebookId() === null) && ($user->getGoogleId() === null) && ($user->getVkontakteId() === null)) {
            return true;
        }

        return false;
    }
}