<?php

namespace AppBundle\Security;

use AppBundle\Entity\Task;
use Goat\AccountBundle\Security\User\GoatUser;
use MakinaCorpus\ACL\Permission;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Task && (
            $attribute === Permission::VIEW ||
            $attribute === Permission::UPDATE ||
            $attribute === Permission::DELETE
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var \AppBundle\Entity\Task $subject */
        $account = $token->getUser();

        return $account instanceof GoatUser && $account->getAccount()->getId() === $subject->getAccountId();
    }
}
