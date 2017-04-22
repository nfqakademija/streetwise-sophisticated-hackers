<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Entity\News;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class NewsVoter
 * @package AppBundle\Security
 */
class NewsVoter extends Voter
{
    /**
     * @var string EDIT
     */
    const EDIT = 'edit';

    /**
     * @var string LIST_ITEM
     */
    const LIST_ITEM = 'list';

    /**
     * @var string SHOW
     */
    const SHOW = 'show';

    /**
     * @var string DELETE
     */
    const DELETE = 'delete';

    /**
     * @var string NEW_ITEM
     */
    const NEW_ITEM = 'new';

    /**
     * {@inheritdoc}
     *
     * @param string $attribute
     * @param News $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array(
            $attribute,
            [
                self::EDIT,
                self::LIST_ITEM,
                self::SHOW,
                self::DELETE,
                self::NEW_ITEM,
            ]
        )) {
            return false;
        }

        // only vote on News objects inside this voter
        if (!$subject instanceof News) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $attribute
     * @param News $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a User object, thanks to supports
        /** @var User $subject */
        switch ($attribute) {
            case self::LIST_ITEM:
            case self::SHOW:
                return true;
            case self::EDIT:
            case self::DELETE:
                return $this->canEdit($subject, $user);
            case self::NEW_ITEM:
                return $this->canCreate($user);
            default:
                return false;
        }
    }

    /**
     * Grants access to administrators or profile owners
     *
     * @param News $subject
     * @param User $user
     *
     * @return bool
     */
    private function canEdit(News $subject, User $user)
    {
        return ($user->getId() == $subject->getAuthor()->getId() || $user->hasRole('ROLE_LECTOR'));
    }

    /**
     * Grants access to administrators
     *
     * @param User $user
     * @return bool
     */
    private function canCreate(User $user)
    {
        return $user->hasRole('ROLE_ADMIN');
    }
}
