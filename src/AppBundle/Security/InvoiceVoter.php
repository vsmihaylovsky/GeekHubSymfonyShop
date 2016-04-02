<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 4/2/16
 * Time: 4:33 PM
 */

namespace AppBundle\Security;


use AppBundle\Entity\Invoice;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class InvoiceVoter extends Voter
{
    const READ = 'read';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::READ))) {
            return false;
        }

        // only vote on User objects inside this voter
        if (!$subject instanceof Invoice) {
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
        /** @var Invoice $privateMessage */
        $privateMessage = $subject;
        switch($attribute) {
            case self::READ:
                return $this->canReadInvoice($privateMessage, $user);
        }
        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param Invoice $invoice
     * @param User $user
     * @return bool
     */
    private function canReadInvoice(Invoice $invoice, User $user)
    {
        return $invoice->getCustomer() === $user;
    }
}