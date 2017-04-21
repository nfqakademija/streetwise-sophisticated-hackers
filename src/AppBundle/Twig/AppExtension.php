<?php

namespace AppBundle\Twig;

use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use JavierEguiluz\Bundle\EasyAdminBundle\Twig\EasyAdminTwigExtension;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class AppExtension extends EasyAdminTwigExtension
{
    const ADMIN_ACTIONS = ['new', 'edit', 'delete'];

    private $tokenStorage;

    private $decisionManager;

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
        return $functions;
    }

    public function getActionsForCertainItem($view, $entityName, $entity)
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return array();
        }

        $user = $token->getUser();


        $actions = parent::getActionsForItem($view, $entityName);

        if((method_exists($entity, 'getUser') && $entity->getUser()->getId() == $user->getId()) || $this->decisionManager->decide($token, ['ROLE_ADMIN'])) {
            return $actions;
        }

        foreach($this::ADMIN_ACTIONS as $action) {
            unset($actions[$action]);
        }

        return $actions;
    }

    public function isActionEnabled($view, $action, $entityName)
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return false;
        }
        return (parent::isActionEnabled($view, $action, $entityName) && $this->decisionManager->decide($token, ['ROLE_ADMIN']));
    }
}
