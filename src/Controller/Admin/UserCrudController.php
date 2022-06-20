<?php

    namespace App\Controller\Admin;

    use App\Entity\User;
    use App\Form\AddressFormType;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
    use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
    use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
    use Symfony\Component\HttpFoundation\File\UploadedFile;

    class UserCrudController extends AbstractCrudController
    {
        public static function getEntityFqcn(): string
        {
            return User::class;
        }

        public function configureFields(string $pageName): iterable
        {
            yield FormField::addPanel("Mis datos")
                ->collapsible();
            yield TextField::new('name')
                ->setColumns(5);
            yield TelephoneField::new('phone')
                ->setColumns(2);
            yield FormField::addRow('');
            yield EmailField::new('email')
                ->setColumns(5);
            yield ImageField::new('logo', 'Logo')
                ->setUploadDir('public/uploads/logos')
                ->setBasePath('uploads/logos')
                ->setUploadedFileNamePattern(
                    fn(UploadedFile $file): string => sprintf('logo%d.%s',
                        $_SESSION['user_data']->getId(),
                        $file->getClientOriginalExtension())
                )
                ->setColumns(4);
            yield TextareaField::new('footer')
                ->setHelp("Este será el pie de página que aparecerá en los albaranes.
            Puede utilizarlo para indicar, por ejemplo , su política de protección de datos.")
                ->hideOnIndex();
            yield TextareaField::new('emailText')
                ->setHelp("Este será el texto del correo electrónico junto al que se enviarán los albaranes
            en la opción <b>Enviar PDF</b>.")
                ->hideOnIndex();

            yield FormField::addPanel("Dirección")
                ->collapsible();
            yield Field::new('address')
                ->setFormType(AddressFormType::class)
                ->setColumns(8);
        }

        public function configureActions(Actions $actions): Actions
        {
            return parent::configureActions($actions)
                ->add(Crud::PAGE_EDIT, Action::DETAIL)

                ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, function (Action $action) {
                    $action->displayIf(function () {
                        return false;
                    });
                    return $action;
                })
                ->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) {
                    $action->displayIf(function () {
                        return false;
                    });
                    return $action;
                })
                ->disable(Crud::PAGE_INDEX)
                ->disable(Crud::PAGE_NEW);
        }

        public function configureCrud(Crud $crud): Crud
        {
            return parent::configureCrud($crud)
                ->setPageTitle(Crud::PAGE_DETAIL, 'Datos de la empresa')
                ->setPageTitle(Crud::PAGE_EDIT, 'Modificar datos de la empresa');
        }

    }
