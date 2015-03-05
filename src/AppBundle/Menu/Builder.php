<?php

namespace AppBundle\Menu;

use AppBundle\Entity\User;
use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root')
            ->setChildrenAttribute('class', 'nav navbar-nav');

        /** @var AuthorizationChecker $authorizationChecker */
        $authorizationChecker = $this->container->get('security.authorization_checker');
        if ($authorizationChecker->isGranted(['IS_AUTHENTICATED_REMEMBERED'])) {
            $menu->addChild('Teams', ['route' => 'team_index']);
            $menu['Teams']->addChild('Overview', ['route' => 'team_index']);
            $menu['Teams']->addChild('Create', ['route' => 'team_new']);
            $menu['Teams']->addChild('Show', ['route' => 'team_show', 'routeParameters' => ['id' => 0]]);
            $menu['Teams']->addChild('Edit', ['route' => 'team_edit', 'routeParameters' => ['id' => 0]]);
            $menu['Teams']->addChild('Delete', ['route' => 'team_delete', 'routeParameters' => ['id' => 0]]);

            /** @var TokenStorage $tokenStorage */
            $tokenStorage = $this->container->get('security.token_storage');
            /** @var User $user */
            $user = $tokenStorage->getToken()->getUser();
            if ($user->getMemberships()->count() > 0) {
                $menu->addChild('Projects', ['route' => 'project_index']);
                $menu->addChild('Tasks', ['route' => 'task_index']);
                $menu->addChild('Time Entries', ['route' => 'timeentry_index']);
                $menu->addChild('Absences', ['route' => 'absence_index']);
            }
        }

        return $menu;
    }

    public function userMenu(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root')
            ->setChildrenAttribute('class', 'nav navbar-nav navbar-right');

        /** @var AuthorizationChecker $authorizationChecker */
        $authorizationChecker = $this->container->get('security.authorization_checker');
        if ($authorizationChecker->isGranted(['IS_AUTHENTICATED_REMEMBERED'])) {
            /** @var TokenStorage $tokenStorage */
            $tokenStorage = $this->container->get('security.token_storage');
            $username = $tokenStorage->getToken()->getUser()->getUsername();
            $menu->addChild($username, ['route' => 'fos_user_profile_edit', 'currentClass' => 'active']);
            $menu->addChild('Logout', ['route' => 'fos_user_security_logout']);
        } else {
            $menu->addChild('Login', ['route' => 'fos_user_security_login', 'currentClass' => 'active']);
            $menu->addChild('Register', ['route' => 'fos_user_registration_register', 'currentClass' => 'active']);
        }

        return $menu;
    }
}