<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Entity\DeliveryNote;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    /* pongo este y no REMEMBERED por seguridad => app que podemos abrir en el cliente
     * y así si se nos olvida hacer logout, si se mete el cliente desde el mismo navegador,
     * no podrá entrar en mi sesión de la app
     */
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/delivery_notes', name: 'app_delivery_notes')]
    public function index(): Response
    {
        $_SESSION['user_data'] = $this->getUser();
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        // Option 1. Make your dashboard redirect to the same page for all users
        return $this->redirect(
            $adminUrlGenerator->setController(DeliveryNoteCrudController::class)
                ->generateUrl()
        );

        /*// Option 2. Make your dashboard redirect to different pages depending on the user
        if ('jane' === $this->getUser()->getUsername()) {
            return $this->redirect('...');
        }*/
        //return $this->render('admin/index.html.twig');
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
        yield MenuItem::linkToDashboard('Configuración', 'fa fa-gears');
        yield MenuItem::section();
        yield MenuItem::linkToLogout('Logout', 'fa fa-arrow-right-from-bracket');
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE);
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

