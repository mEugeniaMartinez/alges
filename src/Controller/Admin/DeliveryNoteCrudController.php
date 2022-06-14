<?php

namespace App\Controller\Admin;

use App\Entity\DeliveryNote;
use App\Form\ClientFormType;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DeliveryNoteCrudController extends AbstractCrudController
{
    /*private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }*/

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
                    ->setParameter('user',$_SESSION['user_data']);
            });
        /*
        yield Field::new('client')
            ->setFormType(ClientFormType::class);*/

        yield FormField::addPanel('Detalles de la intervención')
            ->collapsible();
        yield TextField::new('number', 'Nº Albarán')
            ->hideOnForm();
        yield TextField::new('timeSpent', 'Tiempo empleado')
            ->hideOnIndex()
            ->setColumns(3);
        yield DateField::new('date')
            ->setFormat('dd-MM-Y')
            ->setTextAlign('center');
        yield TextareaField::new('material', 'Material entregado')
            ->hideOnIndex()
            //->setHelp('Puedes utilizar markdown: ## Título, ### Subtítulo, **texto** para negrita, _texto_ para cursiva, etc.')
            ->setFormTypeOptions([
                'row_attr' => [
                    'data-controller' => 'snarkdown',
                ],
                'attr' => [
                    'data-snarkdown-target' => 'input',
                    'data-action' => 'snarkdown#render'
                ],
            ])
            ->setColumns(5);
        yield TextareaField::new('faultDescription', 'Descripción de la avería')
            ->hideOnIndex()
            //->setHelp('Puedes utilizar markdown: ## Título, ### Subtítulo, **texto** para negrita, _texto_ para cursiva, etc.')
            ->setFormTypeOptions([
                'row_attr' => [
                    'data-controller' => 'snarkdown',
                ],
                'attr' => [
                    'data-snarkdown-target' => 'input',
                    'data-action' => 'snarkdown#render'
                ],
            ])
            ->setColumns(5);
        yield TextareaField::new('intervention', 'Intervención')
            ->hideOnIndex()
            ->setHelp('Puedes utilizar markdown: ## Título, ### Subtítulo, **texto** para negrita, _texto_ para cursiva, etc.')
            ->setFormTypeOptions([
                'row_attr' => [
                    'data-controller' => 'snarkdown',
                ],
                'attr' => [
                    'data-snarkdown-target' => 'input',
                    'data-action' => 'snarkdown#render'
                ],
            ])
            ->setColumns(10);

        yield BooleanField::new('signed')
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
            ->renderAsSwitch(false)
            ->setTemplatePath('admin/field/disableDN.html.twig')
            ->setTextAlign('center');
    }

    //TODO - Poner acciones concretas de los albaranes
    public function configureActions(Actions $actions): Actions
    {
        $signAction = Action::new('sign')
            ->addCssClass('btn btn-warning')
            ->setIcon('fas fa-marker')
            ->displayAsButton()
            //->setTemplatePath('admin/sign_action.html.twig')
            ->linkToCrudAction('sign');

        return parent::configureActions($actions)
            ->add(Crud::PAGE_DETAIL, $signAction)
            ->add(Crud::PAGE_EDIT, $signAction)
            //->add('firmar', Action::MYACTION)
            /*->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                $action->displayIf(static function (DeliveryNote $dn) {
                    return !$dn->isSigned();
                });
                return $action;
            })*/;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setPageTitle('new', 'Nuevo albarán')
            ->setPageTitle('index', 'Mis albaranes')
            ->setPageTitle('edit', 'Editar albarán')
            ->setPageTitle('detail', 'Detalles albarán');
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        //Albaranes solo del usuario actual
        $indexQuery = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.user = :user')
            ->setParameter('user',$_SESSION['user_data']);

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
            ->add('client') //TODO - salen tambien los que no son mios -> ¿custom filter? Mini exp. @Cap.29
            ->add('signed')
            ->add('completed')
            ->add('disabled');
    }

}
