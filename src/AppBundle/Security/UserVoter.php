<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class UserVoter
 * @package AppBundle\Security
 */
class UserVoter extends Voter
{
    /**
     * @var string EDIT
     */
    const EDIT = 'edit';

    /**
     * @var string DELETE
     */
    const DELETE = 'delete';

    /**
     * @var string SHOW_ASSIGNMENTS
     */
    const SHOW_ASSIGNMENTS = 'show_assignments';

    /**
     * @var AccessDecisionManagerInterface $decisionManager
     */
    private $decisionManager;

    /**
     * @var ConfigManager $configManager
     */
    private $configManager;

    public function __construct(ConfigManager $configManager, AccessDecisionManagerInterface $decisionManager)
    {
        $this->configManager = $configManager;
        $this->decisionManager = $decisionManager;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $attribute
     * @param User $subject
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
                self::DELETE,
                self::SHOW_ASSIGNMENTS,
            ]
        )) {
            return false;
        }

        // only vote on HasOwnerInterface objects inside this voter
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $attribute
     * @param User $subject
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
            case self::EDIT:
            case self::DELETE:
            case self::SHOW_ASSIGNMENTS:
                return $this->canEditOrDelete($subject, $user, $token);
                break;
            default:
                return false;
        }
    }

    /**
     * Grants access to administrators or profile owners
     *
     * @param User $subject
     * @param User $user
     * @param TokenInterface $token
     *
     * @return bool
     */
    private function canEditOrDelete(User $subject, User $user, TokenInterface $token)
    {
        return (
            $user == $subject->getOwner() ||
            (
                $this->decisionManager->decide($token, ['ROLE_ADMIN']) &&
                !in_array('ROLE_ADMIN', $subject->getRoles()) &&
                !in_array('ROLE_SUPER_ADMIN', $subject->getRoles())
            ) ||
            $this->decisionManager->decide($token, ['ROLE_SUPER_ADMIN'])
        );
    }
}
