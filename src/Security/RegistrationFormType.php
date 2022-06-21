<?php

    namespace App\Security;

    use App\Entity\User;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\Length;
    use Symfony\Component\Validator\Constraints\NotBlank;

    class RegistrationFormType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('name', TextType::class)
                ->add('email')
                ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options' => array(
                        'mapped' => false,
                        'attr' => ['autocomplete' => 'new-password'],
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Introduzca una contraseña',
                            ]),
                            new Length([
                                'min' => 4,
                                'minMessage' => 'La contraseña debe tener al menos {{ limit }} carácteres',
                                // max length allowed by Symfony for security reasons
                                'max' => 4096,
                            ]),
                        ]),
                    'second_options' => array(
                        'mapped' => false,
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Repita la contraseña',
                            ]),
                        ]),
                    'invalid_message' => 'Las contraseñas deben coincidir',
                ));
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => User::class,
            ]);
        }
    }
