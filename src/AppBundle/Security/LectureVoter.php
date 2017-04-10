<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Entity\Lecture;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class LectureVoter
 * @package AppBundle\Security
 */
class LectureVoter extends Voter
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
     * @param Lecture $subject
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

        // only vote on Lecture objects inside this voter
        if (!$subject instanceof Lecture) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $attribute
     * @param Lecture $subject
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
     * @param Lecture $subject
     * @param User $user
     *
     * @return bool
     */
    private function canEdit(Lecture $subject, User $user)
    {
        if($subject !== null) {
            return ($user->getId() == $subject->getLecturer()->getId() || $user->hasRole('ROLE_LECTOR'));
        } else {
            return ($user->hasRole('ROLE_LECTOR'));
        }

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

    protected function getSupportedClasses()
    {
        return array('AppBundle\Entity\Lecture');
    }
}
