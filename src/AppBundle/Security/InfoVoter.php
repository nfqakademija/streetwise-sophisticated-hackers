<?php

namespace AppBundle\Security;

use AppBundle\Entity\HasStudentGroupInterface;
use AppBundle\Entity\User;
use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

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

    private function canShow(HasStudentGroupInterface $subject, User $user, TokenInterface $token)
    {
        // Grants access to administrators, lectors or owners
        if ($user === $subject->getOwner() ||
            $this->decisionManager->decide($token, ['ROLE_ADMIN']) ||
            $this->decisionManager->decide($token, ['ROLE_LECTOR'])) {
            return true;
        }
        // Grants access to 'public' entities
        if ($subject->getStudentGroup() == null) {
            return true;
        }
        // Grants access to users in the same StudentGroup
        if ($user->getStudentgroup() != null) {
            if ($subject->getStudentGroup() === $user->getStudentgroup()) {
                return true;
            }
        }
        return false;
    }
}
