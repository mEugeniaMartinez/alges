<?php

    namespace App\Controller\Admin;

    use App\Entity\Client;
    use App\Entity\DeliveryNote;
    use App\Entity\User;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
    use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
    use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
    use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\User\UserInterface;

    class DashboardController extends AbstractDashboardController
    {
        #[IsGranted('IS_AUTHENTICATED_FULLY')]
        #[Route('/delivery_notes', name: 'app_delivery_notes')]
        public function index(): Response
        {
            $_SESSION['user_data'] = $this->getUser();
            $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

            return $this->redirect(
                $adminUrlGenerator->setController(DeliveryNoteCrudController::class)
                    ->generateUrl()
            );
        }

        public function configureDashboard(): Dashboard
        {
            return Dashboard::new()
                ->renderContentMaximized()
                ->setTitle('<img src="logo_title2.svg" width="75%" alt="logo" id="titulo_logo_menu" style="margin-left: 15px">')
                ->setFaviconPath('logo.ico');

        }

        public function configureUserMenu(UserInterface $user): UserMenu
        {
            return parent::configureUserMenu($user)
                ->setAvatarUrl('user_icon.png');
        }

        public function configureMenuItems(): iterable
        {
            yield MenuItem::section('Gestión');
            yield MenuItem::linkToCrud('Albaranes', 'far fa-file-lines', DeliveryNote::class)
                ->setDefaultSort(['date' => 'DESC']);
            yield MenuItem::linkToCrud('Clientes', 'fa fa-people-group', Client::class);
            yield MenuItem::section('Usuario');
            yield MenuItem::linkToCrud('Configuración', 'fa fa-gears', User::class)
                ->setAction(Crud::PAGE_DETAIL)
                ->setEntityId($_SESSION['user_data']->getId());
            yield MenuItem::section();
            yield MenuItem::linkToLogout('Logout', 'fa fa-arrow-right-from-bracket');
        }

        public function configureActions(): Actions
        {
            return parent::configureActions()
                ->add(Crud::PAGE_INDEX, Action::DETAIL)
                ->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE)
                ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                    return $action->setIcon('fas fa-trash-alt')->setLabel(false);
                })
                ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                    return $action->setIcon('fas fa-pen')->setLabel(false);
                })
                ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                    return $action->setIcon('fas fa-eye')->setLabel(false);
                });
        }

        public function configureAssets(): Assets
        {
            return parent::configureAssets()
                ->addWebpackEncoreEntry('admin');
        }

        public function configureCrud(): Crud
        {
            return parent::configureCrud()
                ->showEntityActionsInlined()
                ->setPaginatorPageSize(10);
        }

    }

