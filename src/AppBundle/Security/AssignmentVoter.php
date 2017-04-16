<?php

namespace AppBundle\Security;
use AppBundle\Entity\Assignment;
use AppBundle\Entity\Homework;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class AssignmentVoter
 * @package AppBundle\Security
 */
class AssignmentVoter extends Voter
{
    /**
     * @var string GRADE
     */
    const GRADE = 'grade';

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
     * @param Homework $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array(
            $attribute,
            [
                self::GRADE,
                self::LIST_ITEM,
                self::SHOW,
                self::DELETE,
                self::NEW_ITEM,
            ]
        )) {
            return false;
        }

        // only vote on Lecture objects inside this voter
        if (!$subject instanceof Assignment) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $attribute
     * @param Homework $subject
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
        /** @var Assignment $subject */
        switch ($attribute) {
            case self::LIST_ITEM:
                return false;
            case self::SHOW:
                return $this->canView($subject, $user);
            case self::GRADE:
                return $this->canGrade($subject, $user);
            case self::DELETE:
                return false;
            case self::NEW_ITEM:
                return $this->canCreate($user);
            default:
                return false;
        }
    }

    /**
     * Grants access to administrators or owners
     *
     * @param Assignment $subject
     * @param User $user
     *
     * @return bool
     */
    private function canEdit(Assignment $subject, User $user)
    {
        //return ($user->getId() == $subject->getLecturer()->getId() || $user->hasRole('ROLE_LECTOR'));
        return false;
    }

    /**
     * @param Assignment $subject
     * @param User $user
     * @return bool
     */
    private function canView(Assignment $subject, User $user)
    {
        return ($user->getId() == $subject->getStudent()->getId() || !$user->isStudent());
    }

    /**
     * Can create if student
     *
     * @param User $user
     * @return bool
     */
    private function canCreate(User $user)
    {
        return $user->isStudent();
    }

    public function canGrade(Assignment $subject, User $user)
    {
        return $user == $subject->getHomework()->getLecturer();
    }
}
