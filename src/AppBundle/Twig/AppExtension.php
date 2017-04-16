<?php

namespace AppBundle\Twig;

/**
 * Class AppExtension
 * @package AppBundle\Twig
 */
class AppExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('has_common', array($this, 'hasCommonElementsFunction')),
        );
    }

    /**
     * @param array $userRoles
     * @param array $actionRoles
     * @return bool
     */
    public function hasCommonElementsFunction(array $userRoles, array $actionRoles)
    {
        $common = array_intersect($userRoles, $actionRoles);

        return !empty($common);
    }

}
