<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        return $this->render('admin/index.html.twig');

        //return parent::index(); //comentado para rediridir a mi dashboard que si tiene navbar

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('AlGes')
            ->setFaviconPath('logo.ico');

    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Gestión');
        yield MenuItem::linkToDashboard('Albaranes', 'far fa-file-lines');
        yield MenuItem::linkToDashboard('Clientes', 'fa fa-people-group');
        yield MenuItem::section('Usuario');
        yield MenuItem::linkToDashboard('Configuración', 'fa fa-gears');
        yield MenuItem::section();
        yield MenuItem::linkToLogout('Logout', 'fa fa-arrow-right-from-bracket');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}

