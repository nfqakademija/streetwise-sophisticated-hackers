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
                self::LIST_ITEM,
                self::SHOW,
                self::DELETE,
                self::NEW_ITEM,
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
            case self::LIST_ITEM:
            case self::SHOW:
                return true;
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
            ($this->decisionManager->decide($token, ['ROLE_ADMIN']) && !in_array('ROLE_ADMIN', $subject->getRoles())) ||
            $this->decisionManager->decide($token, ['ROLE_SUPER_ADMIN'])
        );
    }

    /**
     * Grants access to administrators
     *
     * @param User $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    private function canCreate(User $subject, TokenInterface $token)
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
