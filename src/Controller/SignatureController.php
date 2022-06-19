<?php

    namespace App\Controller;

    use App\Controller\Admin\DeliveryNoteCrudController;
    use Doctrine\ORM\EntityManagerInterface;
    use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Routing\Loader\Configurator\Traits\AddTrait;

    class SignatureController extends AbstractController
    {
        private $em;
        private AdminUrlGenerator $adminUrlGenerator;
        public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
        {
            $this->em = $entityManager;
            $this->adminUrlGenerator = $adminUrlGenerator;
        }

        #[Route('/sign/{id}', name: 'app_sign', methods: ['GET', 'POST'])]
        public function index(int $id, ?string $img): Response
        {
            /*$routeParams = $_GET['routeParams'];
            $dnId = $routeParams["id"];*/

            return $this->render('sign_action.html.twig', [
                'dnId' => $id,
            ]);

        }

/*
        #[Route('/sign/saved', name: 'app_save_sign')]
        public function new(Request $request): Response
        {
            echo "<script>console.log('Debug Objects: " . $request . "' );</script>";

            return $this->render('base.html.twig', [
                'body' => $request,
            ]);*/
          /*  $routeParams = $_GET['routeParams'];
            $dnId = $routeParams["dnId"];
            $dataSignature = $routeParams['imgBase64'];
            var_dump($dataSignature);*/

            /*return $this->redirect( $this->adminUrlGenerator
                ->setController(DeliveryNoteCrudController::class)
                ->setAction('detail')
                ->setEntityId($dnId)
                ->generateUrl());*/

        /*}*/

    }
