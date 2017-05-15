<?php

namespace AppBundle\Security;

use AppBundle\Entity\HasStudentGroupInterface;
use AppBundle\Entity\User;
use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class InfoVoter
 * @package AppBundle\Security
 */
class InfoVoter extends Voter
{
    /**
     * @var string SHOW
     */
    const SHOW = 'show';

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
     * @param HasStudentGroupInterface $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array(
            $attribute,
            [
                self::SHOW,
            ]
        )) {
            return false;
        }

        // only vote on HasStudentGroupInterface objects inside this voter
        if (!$subject instanceof HasStudentGroupInterface) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $attribute
     * @param HasStudentGroupInterface $subject
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

        // you know $subject is a HasStudentGroupInterface object, thanks to supports
        /** @var HasStudentGroupInterface $subject */
        switch ($attribute) {
            case self::SHOW:
                return $this->canShow($subject, $user, $token);
                break;
            default:
                return false;
        }
    }

    /**
     * @param HasStudentGroupInterface $subject
     * @param User $user
     * @param TokenInterface $token
     * @return bool
     */
    private function canShow(HasStudentGroupInterface $subject, User $user, TokenInterface $token)
    {
        /*
         * Grants access to lectors, administrators, superadmin (role hierarchy) OR
         * for student from the same group as entity
         */
        return $this->decisionManager->decide($token, ['ROLE_LECTOR']) ||
            $subject->getStudentGroup() == null ||
            ($user->getStudentgroup() !== null &&
                $subject->getStudentGroup() == $user->getStudentgroup());
    }
}
