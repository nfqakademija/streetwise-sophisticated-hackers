<?php
/**
 * Created by PhpStorm.
 * User: eleggua
 * Date: 17.4.8
 * Time: 12.16
 */

namespace AppBundle\Security;


use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    // these strings are just invented: you can use anything
    const EDIT = 'edit';
    const LIST_ITEM = 'list';
    const SHOW = 'show';
    const DELETE = 'delete';
    const NEW_ITEM = 'new';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(
            self::EDIT,
            self::LIST_ITEM,
            self::SHOW,
            self::DELETE,
            self::NEW_ITEM,
        ))) {
            return false;
        }

        // only vote on User objects inside this voter
        if (!$subject instanceof User && !$subject === null) {
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

        // you know $subject is a User object, thanks to supports
        /** @var User $subject */
        //$post = $subject;

        switch ($attribute) {
            case self::LIST_ITEM:
            case self::SHOW:
                return true;
            case self::EDIT:
            case self::DELETE:
                return $this->canEdit($subject, $user);
            case self::NEW_ITEM:
                return $this->canCreate($user);
        }

        throw new \LogicException('This code should not be reached!');
    }


    private function canEdit(User $subject, User $user)
    {
        return ($user->getId() == $subject->getId() || $user->hasRole('ROLE_ADMIN'));
    }

    private function canCreate(User $user)
    {
        return $user->hasRole('ROLE_ADMIN');
    }
}