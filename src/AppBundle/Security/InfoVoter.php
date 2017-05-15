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
                self::EDIT,
                self::LIST_ITEM,
                self::SHOW,
                self::DELETE,
                self::NEW_ITEM,
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
        /** @var User $subject */
        switch ($attribute) {
            case self::LIST_ITEM:
                return true;
                break;
            case self::SHOW:
                return $this->canShow($subject, $user, $token);
                break;
            case self::EDIT:
            case self::DELETE:
                return $this->canEditOrDelete($subject, $user, $token);
                break;
            case self::NEW_ITEM:
                return $this->canCreate($subject, $token);
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

    /**
     * Grants access to administrators or profile owners
     *
     * @param HasStudentGroupInterface $subject
     * @param User $user
     * @param TokenInterface $token
     *
     * @return bool
     */
    private function canEditOrDelete(HasStudentGroupInterface $subject, User $user, TokenInterface $token)
    {
        return ($user === $subject->getOwner() ||
            $this->decisionManager->decide($token, ['ROLE_ADMIN']));
    }

    /**
     * Grants access to administrators
     *
     * @param HasStudentGroupInterface $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    private function canCreate(HasStudentGroupInterface $subject, TokenInterface $token)
    {
        $reflect = new \ReflectionClass($subject);
        $entityName = $reflect->getShortName();
        $accessRole = $this->getAccessRole($entityName);

        return $this->decisionManager->decide($token, [$accessRole]);
    }

    /**
     * @param string $entityName
     *
     * @return string
     */
    private function getAccessRole(string $entityName)
    {
        if (isset($this->configManager->getEntityConfig($entityName)['access_role'])) {
            return $this->configManager->getEntityConfig($entityName)['access_role'];
        }

        return 'ROLE_ADMIN';
    }
}
