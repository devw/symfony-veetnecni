<?php

namespace Incenteev\WebBundle\Menu;

use Incenteev\WebBundle\Entity\Contest;
use Incenteev\WebBundle\Entity\User;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class MenuBuilder extends ContainerAware
{

    /**
     * Menu displayed on contest pages
     *
     * @param FactoryInterface $factory
     * @param array            $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function contestMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav menu-sidebar-content');

        if (empty($options['contest'])) {
            throw new \InvalidArgumentException(sprintf('Parameter "contest" must be defined to render the menu.'));
        }

        /** @var $contest \Incenteev\WebBundle\Entity\Contest */
        $contest = $options['contest'];
        $contestId = (string) $contest->getId();

        /** @var $securityContext \Symfony\Component\Security\Core\SecurityContextInterface */
        $securityContext = $this->container->get('security.context');
        $token = $securityContext->getToken();

        if (null === $token) {
            return false;
        }

        /** @var $user User */
        $user = $token->getUser();

        $isOwner = $contest->hasOwner($user);

        // Display Section
        $menuDisplay = $menu->addChild('display', array(
            'display' => false,
            'label' => 'contest.menu_header.display',
            'labelAttributes' => array(
                'class' => 'menu-header',
            ),
        ));

        $menuDisplay->addChild('contest_show', array(
            'route' => 'contest_show',
            'routeParameters' => array('id' => $contestId),
            'label' => 'contest.menu.results',
            'linkAttributes' => array(
                'class' => 'menu-link',
            ),
            'labelAttributes' => array(
                'class' => 'menu-link',
            ),
            'extras' => array(
                'icon' => 'sprite-icon-user',
            ),
        ));

        $hasTeams = null !== $this->getContestTeamRepository()->findOneBy(array('contest' => $contestId));

        if ($hasTeams) {
            $menuDisplay->addChild('contest_show_teams', array(
                'route' => 'contest_show_teams',
                'routeParameters' => array('id' => $contestId),
                'label' => 'contest.menu.team_results',
                'linkAttributes' => array(
                    'class' => 'menu-link',
                ),
                'labelAttributes' => array(
                    'class' => 'menu-link',
                ),
                'extras' => array(
                    'icon' => 'sprite-icon-users',
                ),
            ));
        }

        $menuDisplay->addChild('contest_details', array(
            'route' => 'contest_details',
            'routeParameters' => array('id' => $contestId),
            'label' => 'contest.menu.about',
            'linkAttributes' => array(
                'class' => 'menu-link',
            ),
            'labelAttributes' => array(
                'class' => 'menu-link',
            ),
            'extras' => array(
                'icon' => 'sprite-icon-details',
            ),
        ));

        // Manager Section
        $menuManage = $menu->addChild('manage', array(
            'display' => false,
            'label' => 'contest.menu_header.manage',
            'labelAttributes' => array(
                'class' => 'menu-header',
            ),
        ));

        $menuManage->addChild('contest_data_show_results', array(
            'display' => false,
            'route' => 'contest_data_show_results',
            'routeParameters' => array('id' => $contestId),
            'label' => 'contest.menu.update_all_results',
            'linkAttributes' => array(
                'class' => 'menu-link',
            ),
            'labelAttributes' => array(
                'class' => 'menu-link',
            ),
            'extras' => array(
                'icon' => 'sprite-icon-results',
            ),
        ));

        $menuManage->addChild('contest_submit_data', array(
            'display' => false,
            'route' => 'contest_submit_data',
            'routeParameters' => array('id' => $contestId),
            'label' => 'contest.menu.update_data',
            'linkAttributes' => array(
                'class' => 'menu-link',
            ),
            'labelAttributes' => array(
                'class' => 'menu-link',
            ),
            'extras' => array(
                'icon' => 'sprite-icon-results',
            ),
        ));

        if ($isOwner) {
            $menuManage['contest_data_show_results']->setDisplay(true);
        }

        if ($contest->isUpdatedByParticipants() && !$isOwner) {
            $menuManage['contest_submit_data']->setDisplay(true);
        }

        // Adding the "Set Up" section if the current user is an owner
        if ($isOwner) {
            // Setup Section
            $menuSetup = $menu->addChild('set_up', array(
                'label' => 'contest.menu_header.set_up',
                'labelAttributes' => array(
                    'class' => 'menu-header',
                ),
            ));

            $menuSetup->addChild('contest_settings_general', array(
                'route' => 'contest_settings_general',
                'routeParameters' => array('id' => $contestId),
                'label' => 'contest.menu.general',
                'linkAttributes' => array(
                    'class' => 'menu-link',
                    'data-tid' => 'pjax',
                ),
                'labelAttributes' => array(
                    'class' => 'menu-link',
                ),
                'extras' => array(
                    'icon' => 'sprite-icon-edit',
                ),
            ));

            $menuSetup->addChild('contest_settings_appearance', array(
                'route' => 'contest_settings_appearance',
                'routeParameters' => array('id' => $contestId),
                'label' => 'contest.menu.appearance',
                'linkAttributes' => array(
                    'class' => 'menu-link',
                    'data-tid' => 'pjax',
                ),
                'labelAttributes' => array(
                    'class' => 'menu-link',
                ),
                'extras' => array(
                    'icon' => 'sprite-icon-appearance',
                ),
            ));

            $menuSetup->addChild('contest_settings_prizes', array(
                'route' => 'contest_settings_prizes',
                'routeParameters' => array('id' => $contestId),
                'label' => 'contest.menu.prizes',
                'linkAttributes' => array(
                    'class' => 'menu-link',
                    'data-tid' => 'pjax',
                ),
                'labelAttributes' => array(
                    'class' => 'menu-link',
                ),
                'extras' => array(
                    'icon' => 'sprite-icon-prize',
                ),
            ));

            $menuSetup->addChild('contest_settings_invite', array(
                'route' => 'contest_settings_invite',
                'routeParameters' => array('id' => $contestId),
                'label' => 'contest.menu.participants',
                'linkAttributes' => array(
                    'class' => 'menu-link',
                    'data-tid' => 'pjax',
                ),
                'labelAttributes' => array(
                    'class' => 'menu-link',
                ),
                'extras' => array(
                    'icon' => 'sprite-icon-user',
                ),
            ));

            $menuSetup->addChild('contest_teams', array(
                'route' => 'contest_settings_teams',
                'routeParameters' => array('id' => $contestId),
                'label' => 'contest.menu.teams',
                'linkAttributes' => array(
                    'class' => 'menu-link',
                    'data-tid' => 'pjax',
                ),
                'labelAttributes' => array(
                    'class' => 'menu-link',
                ),
                'extras' => array(
                    'icon' => 'sprite-icon-users',
                ),
            ));

            $menuSetup->addChild('contest_settings_data', array(
                'route' => 'contest_settings_data',
                'routeParameters' => array('id' => $contestId),
                'label' => 'contest.menu.data',
                'linkAttributes' => array(
                    'class' => 'menu-link',
                    'data-tid' => 'pjax',
                ),
                'labelAttributes' => array(
                    'class' => 'menu-link',
                ),
                'extras' => array(
                    'icon' => 'sprite-icon-data',
                ),
            ));

            $menuSetup->addChild('contest_settings_email_content', array(
                'route' => 'contest_settings_email_content',
                'routeParameters' => array('id' => $contestId),
                'label' => 'contest.menu.email_content',
                'linkAttributes' => array(
                    'class' => 'menu-link',
                    'data-tid' => 'pjax',
                ),
                'labelAttributes' => array(
                    'class' => 'menu-link',
                ),
                'extras' => array(
                    'icon' => 'sprite-icon-mail',
                ),
            ));

            $menuSetup->addChild('contest_settings_summary', array(
                'route' => 'contest_settings_summary',
                'routeParameters' => array('id' => $contestId),
                'label' => 'contest.menu.launch',
                'linkAttributes' => array(
                    'class' => 'menu-link',
                    'data-tid' => 'pjax',
                ),
                'labelAttributes' => array(
                    'class' => 'menu-link',
                ),
                'extras' => array(
                    'icon' => 'sprite-icon-summary',
                ),
            ));
        }

        // Managing the contest publishing
        if ($contest->isPublished()) {
            $menuDisplay->setDisplay(true);
            $menuManage->setDisplay(true);
        } else {
            $menuPreview = $menu->addChild('preview', array(
                'label' => 'contest.menu_header.preview',
                'labelAttributes' => array(
                    'class' => 'menu-header',
                ),
            ));

            $menuPreview->addChild('contest_preview', array(
                'route' => 'contest_show',
                'routeParameters' => array('id' => $contestId),
                'label' => 'contest.menu.preview',
                'linkAttributes' => array(
                    'class' => 'menu-link',
                ),
                'labelAttributes' => array(
                    'class' => 'menu-link',
                ),
                'extras' => array(
                    'icon' => 'sprite-icon-eye',
                ),
            ));
        }

        return $menu;
    }

    /**
     * Steps Menu used during the confirmation process
     *
     * @param FactoryInterface $factory
     * @param array            $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function contestConfirmationMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'breadcrumb breadcrumb-wizard');

        if (empty($options['participation'])) {
            throw new \InvalidArgumentException(sprintf('Parameter "participation" must be defined to render the menu.'));
        }

        /** @var $participation \Incenteev\WebBundle\Entity\Participation */
        $participation = $options['participation'];
        $user = $participation->getUser();

        /** @var $request Symfony\Component\HttpFoundation\Request */
        $request = $this->container->get('request');
        $isFullProcess = $request->query->get('full-process');

        // Defining menu items
        $menuItems = array();

        if ($isFullProcess) {
            $menuItems['general_information'] = array(
                'route' => 'confirmation_with_registration',
                'label' => 'confirmation.step.general_information',
            );
        }

        $menuItems['participation_confirmation'] = array(
            'route' => 'confirmation_acceptance',
            'label' => 'confirmation.step.participation_confirmation',
        );

        $menuItems['choose_avatar'] = array(
            'route' => 'confirmation_choose_avatar',
            'label' => 'confirmation.step.choose_avatar',
        );

        // Defining children
        foreach ($menuItems as $itemKey => $itemValue) {
            $menu->addChild($itemKey, array(
                'label' => $itemValue['label'],
                'extras' => array(
                    'routes' => array($itemValue['route']),
                ),
            ));
        }

        return $menu;
    }

    /**
     * Menu used in the organization administration
     *
     * @param FactoryInterface $factory
     * @param array            $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function organizationAdministrationMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav menu-sidebar-content');

        // Defining menu items
        $menu->addChild('organization', array(
            'route' => 'organization_edit',
            'label' => 'admin.menu.organization',
            'linkAttributes' => array(
                'class' => 'menu-link',
            ),
            'labelAttributes' => array(
                'class' => 'menu-link',
            ),
            'extras' => array(
                'icon' => 'sprite-icon-edit',
            ),
        ));

        $menu->addChild('users', array(
            'route' => 'admin_user_list',
            'label' => 'admin.menu.users',
            'linkAttributes' => array(
                'class' => 'menu-link',
            ),
            'labelAttributes' => array(
                'class' => 'menu-link',
            ),
            'extras' => array(
                'icon' => 'sprite-icon-user',
            ),
        ));

        $menu->addChild('teams', array(
            'route' => 'admin_team_list',
            'label' => 'admin.menu.teams',
            'linkAttributes' => array(
                'class' => 'menu-link',
            ),
            'labelAttributes' => array(
                'class' => 'menu-link',
            ),
            'extras' => array(
                'icon' => 'sprite-icon-users',
            ),
        ));

        return $menu;
    }

    /**
     * Checks if a user can submit data.
     *
     * @param \Incenteev\WebBundle\Entity\Contest $contest
     *
     * @return boolean
     */
    private function canSubmit(Contest $contest, User $user)
    {
        if ($contest->isPublished() && $contest->isUpdatedByParticipants()) {
            $participationRepository = $this->getParticipationRepository();

            $participation = $participationRepository->findOneBy(array(
                'user' => $user->getId(),
                'contest' => $contest->getId(),
            ));

            if (null !== $participation) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\ContestTeamRepository
     */
    protected function getContestTeamRepository()
    {
        return $this->container->get('doctrine')->getRepository('WebBundle:ContestTeam');
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\ParticipationRepository
     */
    protected function getParticipationRepository()
    {
        return $this->container->get('doctrine')->getRepository('WebBundle:Participation');
    }
}
