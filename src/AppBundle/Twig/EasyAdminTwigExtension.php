<?php
/**
 * Created by PhpStorm.
 * User: eleggua
 * Date: 17.4.8
 * Time: 10.00
 */

namespace AppBundle\Twig;

use JavierEguiluz\Bundle\EasyAdminBundle\Twig\EasyAdminTwigExtension as BaseExtension;

class EasyAdminTwigExtension extends BaseExtension
{
    /**
     * Returns the actions configured for each item displayed in the given view.
     * This method is needed because some actions are displayed globally for the
     * entire view (e.g. 'new' action in 'list' view).
     *
     * @param string $view
     * @param string $entityName
     *
     * @return array
     */
    public function getActionsForItem($view, $entityName)
    {
        try {
            $entityConfig = $this->getConfigManager()->getEntityConfig($entityName);
        } catch (\Exception $e) {
            return array();
        }
        exit();

        $disabledActions = $entityConfig['disabled_actions'];
        $viewActions = $entityConfig[$view]['actions'];

        $actionsExcludedForItems = array(
            'list' => array('new', 'search'),
            'edit' => array(),
            'new' => array(),
            'show' => array(),
        );
        $excludedActions = $actionsExcludedForItems[$view];

        return array_filter($viewActions, function ($action) use ($excludedActions, $disabledActions) {
            return !in_array($action['name'], $excludedActions) && !in_array($action['name'], $disabledActions);
        });
    }
}