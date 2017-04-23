<?php

namespace AppBundle\Twig;

use AppBundle\Entity\HasOwnerInterface;
use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use JavierEguiluz\Bundle\EasyAdminBundle\Twig\EasyAdminTwigExtension;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

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
        $debug = false,
        TokenStorageInterface $tokenStorage,
        AccessDecisionManagerInterface $decisionManager
    )
    {
        parent::__construct($configManager, $propertyAccessor, $debug);
        $this->tokenStorage = $tokenStorage;
        $this->decisionManager = $decisionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $functions = parent::getFunctions();
        $functions[] = new \Twig_SimpleFunction('custom_get_actions_for_*_item', array($this, 'getActionsForCertainItem'));
        $functions[] = new \Twig_SimpleFunction('user_has_access', array($this, 'userHasLectorAccess'));

        return $functions;
    }

    public function getFilters()
    {
        $filters = parent::getFilters();
        $filters[] = new \Twig_SimpleFilter('gravatar', array($this, 'gravatarFilter'), array('is_safe' => array('html')));

        return $filters;
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
        if (null === $token = $this->tokenStorage->getToken()) {
            return array();
        }

        $user = $token->getUser();


        $actions = parent::getActionsForItem($view, $entityName);

        if($entity instanceof HasOwnerInterface && $entity->getOwner()->getId() == $user->getId() || $this->decisionManager->decide($token, ['ROLE_ADMIN'])) {
            return $actions;
        }

        foreach($this::ADMIN_ACTIONS as $action) {
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

        if($action == 'search')
            return $parent;

        if (null === $token = $this->tokenStorage->getToken()) {
            return false;
        }
        $accessRole = $this->getEntityAccessRole($entityName);

        return ($parent && $this->decisionManager->decide($token, [$accessRole]));
    }

    /**
     * @param string $entityName
     *
     * @return string
     */
    private function getEntityAccessRole(string $entityName)
    {
        if(isset($this->getEntityConfiguration($entityName)['access_role']))
            return $this->getEntityConfiguration($entityName)['access_role'];

        return 'ROLE_ADMIN';
    }

    public function userHasLectorAccess($entity)
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return false;
        }

        $user = $token->getUser();

        return ($entity instanceof HasOwnerInterface && $entity->getOwner()->getId() == $user->getId() ||
            $this->decisionManager->decide($token, ['ROLE_LECTOR']));
    }

    public function gravatarFilter($email)
    {
        return md5($email);
    }
}
