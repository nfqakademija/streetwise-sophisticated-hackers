<?php

namespace AppBundle\Twig;

use AppBundle\Entity\HasOwnerInterface;
use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use JavierEguiluz\Bundle\EasyAdminBundle\Twig\EasyAdminTwigExtension;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;

class AppExtension extends EasyAdminTwigExtension
{
    /**
     * @var array ADMIN_ACTIONS
     */
    const ADMIN_ACTIONS = ['new', 'edit', 'delete'];

    /**
     * @var TokenStorageInterface $tokenStorage
     */
    private $tokenStorage;

    /**
     * @var AccessDecisionManagerInterface $decisionManager
     */
    private $decisionManager;

    /**
     * AppExtension constructor.
     *
     * @param ConfigManager $configManager
     * @param PropertyAccessor $propertyAccessor
     * @param bool $debug
     * @param TokenStorageInterface $tokenStorage
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(
        ConfigManager $configManager,
        PropertyAccessor $propertyAccessor,
        TokenStorageInterface $tokenStorage,
        AccessDecisionManagerInterface $decisionManager,
        LogoutUrlGenerator $logoutUrlGenerator,
        $debug = false
    ) {
        parent::__construct($configManager, $propertyAccessor, $debug, $logoutUrlGenerator);
        $this->tokenStorage = $tokenStorage;
        $this->decisionManager = $decisionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $functions = parent::getFunctions();
        $functions[] = new \Twig_SimpleFunction('custom_get_actions_for_*_item', [
            $this,
            'getActionsForCertainItem',
        ]);
        return $functions;
    }

    /**
     * @param $view
     * @param string $entityName
     * @param $entity
     *
     * @return array
     */
    public function getActionsForCertainItem($view, $entityName, $entity)
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            return array();
        }

        $user = $token->getUser();

        $actions = parent::getActionsForItem($view, $entityName);

        if (($entity instanceof HasOwnerInterface && $entity->getOwner() == $user) ||
            $this->decisionManager->decide($token, ['ROLE_ADMIN'])) {
            return $actions;
        }

        foreach ($this::ADMIN_ACTIONS as $action) {
            unset($actions[$action]);
        }

        return $actions;
    }

    /**
     * @param string $view
     * @param string $action
     * @param string $entityName
     *
     * @return bool
     */
    public function isActionEnabled($view, $action, $entityName)
    {
        $parent = parent::isActionEnabled($view, $action, $entityName);

        if ($action == 'search') {
            return $parent;
        }

        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            return false;
        }

        $accessRole = $this->getAccessRole($entityName);

        return ($parent && $this->decisionManager->decide($token, [$accessRole]));
    }

    /**
     * @param string $entityName
     *
     * @return string
     */
    private function getAccessRole(string $entityName)
    {
        if (isset($this->getEntityConfiguration($entityName)['access_role'])) {
            return $this->getEntityConfiguration($entityName)['access_role'];
        }

        return 'ROLE_ADMIN';
    }
}
