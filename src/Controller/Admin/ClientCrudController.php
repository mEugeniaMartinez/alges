<?php

    namespace App\Controller\Admin;

    use App\Entity\Client;
    use App\Form\AddressFormType;
    use Doctrine\ORM\QueryBuilder;
    use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
    use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
    use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
    use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
    use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
    use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

    class ClientCrudController extends AbstractCrudController
    {
        public static function getEntityFqcn(): string
        {
            return Client::class;
        }

        public function configureFields(string $pageName): iterable
        {
            yield FormField::addPanel("Datos del cliente")
                ->collapsible();
            yield TextField::new('name')
                ->setColumns(6);
            yield TextField::new('cif', 'CIF')
                ->hideOnIndex()
                ->setColumns(2);
            yield EmailField::new('email')
                ->setColumns(6);
            yield TelephoneField::new('phone')
                ->setColumns(2);

            yield FormField::addPanel("Dirección")
                ->collapsible();
            yield Field::new('address')
                ->setFormType(AddressFormType::class)
                ->setRequired(true)
                ->setColumns(8);

        }

        public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
        {
            //Clientes solo del usuario actual
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
                ->andWhere('entity.user = :user')
                ->setParameter('user', $_SESSION['user_data']);
        }

        public function configureFilters(Filters $filters): Filters
        {
            return parent::configureFilters($filters)
                ->add('name');
        }

        public function configureActions(Actions $actions): Actions
        {
            return parent::configureActions($actions)
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
                ->reorder(Crud::PAGE_DETAIL, [Action::EDIT, Action::INDEX, Action::DELETE]);

        }


    }
