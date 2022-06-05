<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Business;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {

        $this->em = $entityManager;
    }

    #[Route('/base', name: 'app_base')]
    public function index(): Response
    {

        /*$address = new Address();
        $address->setStreet("C/ Nueva, 6");

        $business = new Business();
        $business->setName("Nueva empresa");
        $business->setAddress($address);

        $this->em->persist($business);
        $this->em->persist($address);
        $this->em->flush();*/

        $businesRepo = $this->em->getRepository(Business::class);

        $busines = $businesRepo->findAll();

        return $this->render('base.html.twig', [
            'controller_name' => 'BaseController',
            'business' => $busines,
        ]);
    }
}
