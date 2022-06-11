<?php

namespace App\Controller\Admin;

use App\Entity\DeliveryNote;
use App\Repository\DeliveryNoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
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

        /*//TODO - encontrar mejor forma de persistir numero de albarán
        $dnSinNumber = $this->em->getRepository(DeliveryNote::class)
            ->createQueryBuilder('dn')
            ->where('dn.number is null')
            ->andWhere('dn.user = :user')
            ->setParameter('user', $_SESSION['user_data'])
            ->getQuery()
            ->execute();

        foreach ($dnSinNumber as $dn) {
            var_dump($dn);
            $this->em->getRepository(DeliveryNote::class)
                ->createQueryBuilder('dn')
                ->where('dn.id = :id')
                ->setParameter('id', $dn->getid())
                ->set('dn.number', ':number')
                ->setParameter('number', $dn->generateNumber())
                ->update()
                ->getQuery()
                ->execute();
        }*/

    public static function getEntityFqcn(): string
    {
        return DeliveryNote::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Detalles del Cliente');
        yield AssociationField::new('client', 'Cliente')
            ->autocomplete()
            ->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                $queryBuilder->andWhere('entity.user = :user')
                    ->setParameter('user',$_SESSION['user_data']);
            });
        yield TextField::new('number', 'Nº Albarán')
            ->hideOnForm()
            ->setSortable(false); //TODO - Persistir el numero de albaran,a hora solo muestra xd


        yield FormField::addPanel('Detalles de la intervención');
        yield DateField::new('date', 'Fecha')
            ->setFormat('dd-MM-Y')
            ->setTextAlign('center');
        yield TextField::new('timeSpent', 'Tiempo empleado')
            ->hideOnIndex();

        yield TextareaField::new('material', 'Material entregado')
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
            ]);
        yield TextareaField::new('faultDescription', 'Descripción de la avería')
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
            ]);
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
            ]);

        yield BooleanField::new('signed', 'Firmado')
            ->hideOnForm()
            ->renderAsSwitch(false)
            ->setTemplatePath('admin/field/signedDN.html.twig')
            ->setTextAlign('center');
        yield BooleanField::new('completed', 'Estado')
            ->hideOnForm()
            ->renderAsSwitch(false)
            ->setTemplatePath('admin/field/completedDN.html.twig')
            ->setTextAlign('center');
        yield BooleanField::new('disabled', 'Anulado')
            ->renderAsSwitch(false)
            ->setTemplatePath('admin/field/disableDN.html.twig')
            ->setTextAlign('center');
    }

    //TODO - Poner acciones concretas de los albaranes
   /* public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            //->add()
        ; // TODO: Change the autogenerated stub
    }*/

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            //->setSearchFields(['date', 'client', 'number'])
            ->setPageTitle('new', 'Nuevo albarán')
            ->setPageTitle('index', 'Mis albaranes')
            ->setPageTitle('edit', 'Editar albarán')
            ->setPageTitle('detail', 'Detalles albarán');
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {

        //Albaranes solo del usuario actual
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.user = :user')
            ->setParameter('user',$_SESSION['user_data']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add('date')
            ->add('client') //TODO - salen tambien los que no son mios
            ->add('signed')
            ->add('completed')
            ->add('disabled');
    }

}
