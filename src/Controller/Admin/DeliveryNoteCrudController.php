<?php

    namespace App\Controller\Admin;

    use App\Entity\Client;
    use App\Entity\DeliveryNote;
    use App\Service\EmailService;
    use App\Service\PdfService;
    use Doctrine\ORM\EntityManagerInterface;
    use Doctrine\ORM\QueryBuilder;
    use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
    use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
    use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
    use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
    use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
    use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
    use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Mailer\MailerInterface;
    use Twig\Environment;

    class DeliveryNoteCrudController extends AbstractCrudController
    {
        private AdminUrlGenerator $adminUrlGenerator;
        private Environment $twig;
        private PdfService $pdfService;
        private $em;

        public function __construct(AdminUrlGenerator      $adminUrlGenerator,
                                    EntityManagerInterface $entityManager,
                                    Environment            $twig,
                                    MailerInterface        $mailer)
        {
            $this->adminUrlGenerator = $adminUrlGenerator;
            $this->em = $entityManager;
            $this->twig = $twig;
            $this->pdfService = new PdfService($this->twig);
            $this->emailService = new EmailService($mailer, $this->twig);
        }

        public static function getEntityFqcn(): string
        {
            return DeliveryNote::class;
        }

        public function configureFields(string $pageName): iterable
        {
            yield FormField::addPanel('Detalles del Cliente')
                ->collapsible();
            yield AssociationField::new('client', 'Cliente')
                ->autocomplete()
                ->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                    $queryBuilder->andWhere('entity.user = :user')
                        ->setParameter('user', $_SESSION['user_data']);
                });

            yield FormField::addPanel('Detalles de la intervención')
                ->collapsible();
            yield TextField::new('number', 'Nº Albarán')
                ->hideOnForm();
            yield TextField::new('timeSpent', 'Tiempo empleado')
                ->hideOnIndex()
                ->setColumns(2);
            yield DateField::new('date')
                ->setFormat('dd-MM-Y')
                ->setColumns(3)
                ->setTextAlign('center');
            yield ImageField::new('pdf', 'PDF del albarán')
                ->onlyWhenUpdating()
                ->setUploadDir('public/uploads/pdf')
                ->setBasePath('uploads/pdf')
                ->setColumns(3);

            yield FormField::addRow();

            yield BooleanField::new('signed')
                ->setHelp('Marcar como <b>Firmado</b> no permitirá realizar cambios en el albarán.')
                ->onlyWhenUpdating()
                ->renderAsSwitch(false)
                ->setColumns(2);
            yield BooleanField::new('disabled')
                ->onlyWhenUpdating()
                ->renderAsSwitch(false)
                ->setTemplatePath('admin/field/disableDN.html.twig')
                ->setTextAlign('center')
                ->setColumns(2);

            yield FormField::addRow();

            yield TextareaField::new('material', 'Material entregado')
                ->hideOnIndex()
                ->setColumns(5);
            yield TextareaField::new('faultDescription', 'Descripción de la avería')
                ->hideOnIndex()
                ->setColumns(5);
            yield TextareaField::new('intervention', 'Intervención')
                ->hideOnIndex()
                ->setColumns(10);

            yield TextField::new('pdf', 'PDF del albarán')
                ->onlyOnDetail()
                ->setHelp("Si quiere guardar el PDF generado del albarán, entre en <b>Modificar</b> y suba el archivo.");
            yield BooleanField::new('signed')
                ->setHelp('Marcar como <b>Firmado</b> no permitirá realizar cambios en el albarán.')
                ->hideOnForm()
                ->renderAsSwitch(false)
                ->setTemplatePath('admin/field/signedDN.html.twig')
                ->setTextAlign('center');
            yield BooleanField::new('completed')
                ->hideOnForm()
                ->renderAsSwitch(false)
                ->setTemplatePath('admin/field/completedDN.html.twig')
                ->setTextAlign('center');
            yield BooleanField::new('disabled')
                ->hideOnForm()
                ->renderAsSwitch(false)
                ->setTemplatePath('admin/field/disableDN.html.twig')
                ->setTextAlign('center');
        }

        public function configureCrud(Crud $crud): Crud
        {
            return parent::configureCrud($crud)
                ->setPageTitle('new', 'Nuevo albarán')
                ->setPageTitle('index', 'Mis albaranes')
                ->setPageTitle('edit', 'Editar albarán')
                ->setHelp('detail', 'Si los datos de Usuario no están completos, el PDF se generará 
            sin esos datos. Para completar la información de Usuario, vaya al apartado de <b>Configuración</b> del 
            menú lateral.')
                ->setPageTitle('detail', 'Detalles albarán');
        }

        public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
        {
            $indexQuery = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->andWhere('entity.user = :user')
                ->setParameter('user', $_SESSION['user_data']);

            $myDNs = $indexQuery->getQuery()->getResult();

            $dnRepo = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->getEntityManager()
                ->getRepository(DeliveryNote::class);

            foreach ($myDNs as $dn) {
                $dnRepo->updateCompleted($dn);
            }

            return $indexQuery;
        }

        public function configureFilters(Filters $filters): Filters
        {
            return parent::configureFilters($filters)
                ->add('date')
                ->add('client')
                ->add('signed')
                ->add('completed')
                ->add('disabled');
        }

        public function configureActions(Actions $actions): Actions
        {

            /*$signAction = Action::new('sign', 'Firmar', 'fas fa-marker')
                ->setCssClass('btn btn-warning')
                ->linkToUrl(function (DeliveryNote $dn) {
                    //return $this->generateUrl('app_sign');
                    return $this->adminUrlGenerator->setRoute('app_sign', ['id'=>$dn->getId()]);
                });*/

            $pdfAction = $this->getPdfAction();
            $pdfActionIndex = $this->getPdfIndexAction();
            $sendPdfAction = $this->getSendEmailAction();
            $sendPdfActionIndex = $this->getSendEmailIndexAction();


            return parent::configureActions($actions)
                ->add(Crud::PAGE_DETAIL, $pdfAction)
                ->add(Crud::PAGE_INDEX, $pdfActionIndex)
                ->add(Crud::PAGE_DETAIL, $sendPdfAction)
                ->add(Crud::PAGE_INDEX, $sendPdfActionIndex)
                ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER, static function (Action $action) {
                    return $action->setIcon('fa fa-plus-circle');
                })
                ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, static function (Action $action) {
                    return $action->setIcon('fa fa-save');
                })
                ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, static function (Action $action) {
                    return $action->setIcon('fa fa-save');
                })
                ->update(Crud::PAGE_INDEX, Action::NEW, static function (Action $action) {
                    return $action->setIcon('fas fa-plus');
                })
                ->update(Crud::PAGE_DETAIL, Action::INDEX, static function (Action $action) {
                    return $action->setIcon('fas fa-list-ul');
                })
                ->update(Crud::PAGE_DETAIL, Action::EDIT, static function (Action $action) {
                    return $action->setIcon('far fa-edit');
                })
                ->reorder(Crud::PAGE_DETAIL, [Action::EDIT, 'sendPdf', 'pdf', Action::INDEX, Action::DELETE]);
        }

        public function generatePdf(): Response
        {
            $dn = $this->em->getRepository(DeliveryNote::class)
                ->find($_GET['entityId']);
            return new Response($this->pdfService->showPdfFile($dn));
        }

        public function sendPdf(AdminContext $context)
        {
            $dn = $this->em->getRepository(DeliveryNote::class)
                ->find($_GET['entityId']);
            $this->emailService->sendPdfByEmail($dn);
            $this->addFlash('success', 'Email enviado junto al albarán.');
            return $this->redirect('delivery_notes');
        }

        public function getPdfAction(): Action
        {
            return Action::new('pdf', 'Generar PDF', 'fa fa-file-pdf')
                ->setCssClass('btn btn-info')
                ->linkToCrudAction('generatePdf')
                ->displayAsLink()
                ->setHtmlAttributes(['target' => '_blank'])
                ->displayIf(function (DeliveryNote $deliveryNote) {
                    return !$deliveryNote->isDisabled();
                }); //si anulado -> no PDF
        }

        public function getPdfIndexAction(): Action
        {
            return Action::new('pdf', 'Descargar PDF', 'fas fa-file-download')
                ->setLabel(false)
                ->setCssClass('otherActionsIndex')
                ->linkToCrudAction('generatePdf')
                ->displayAsLink()
                ->setHtmlAttributes(['target' => '_blank'])
                ->displayIf(function (DeliveryNote $deliveryNote) {
                    return !$deliveryNote->isDisabled();
                }); //si anulado -> no PDF
        }

        public function getSendEmailAction(): Action
        {
            return Action::new('sendPdf', 'Enviar PDF', 'fas fa-paper-plane')
                ->setCssClass('btn btn-info')
                ->linkToCrudAction('sendPdf')
                ->displayAsLink()
                ->displayIf(function (DeliveryNote $deliveryNote) {
                    return $deliveryNote->isDisabled() || !$deliveryNote->getClient() ? false : true;
                }); //si anulado o sin cliente -> no mail
        }

        public function getSendEmailIndexAction(): Action
        {
            return Action::new('sendPdf', 'Enviar PDF', 'fas fa-paper-plane')
                ->setLabel(false)
                ->setCssClass('otherActionsIndex')
                ->linkToCrudAction('sendPdf')
                ->displayAsLink()
                ->displayIf(function (DeliveryNote $deliveryNote) {
                    return $deliveryNote->isDisabled() || !$deliveryNote->getClient() ? false : true;
                }); //si anulado o sin cliente -> no mail
        }

    }
