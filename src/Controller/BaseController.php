<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Business;
use App\Entity\Client;
use App\Entity\User;
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

        /*$addressRepo = $this->em->getRepository(Address::class);
        $address = $addressRepo->find(16);
        $address2 = $addressRepo->find(17);

        $user = new User();
        $user->setName("User");
        $user->setFooter("cosotas importantes");
        $user->setPhone("666000000");
        $user->setPhone("user@user,com");
        $user->setAddress($address2);

        $client = new Client();
        $client->setName("Cliente");
        $client->setEmail("cliente@cliente.com");
        $client->setPhone("666111111");
        $client->setAddress($address);

        $this->em->persist($client);
        $this->em->persist($user);
        $this->em->flush();*/

        $userRepo = $this->em->getRepository(User::class);

        $userDB = $userRepo->find(26);
        //$clientDB = $clientRepo->find(27);

        /*$clientDB->setUser($userDB);
        $this->em->flush();*/

        $clients = $userDB->getClients();
        foreach ($clients as $c) {
            var_dump($c->getEmail());
        }

        return $this->render('base.html.twig', [
            'controller_name' => 'BaseController',
            //clients' => $clientDB,
        ]);
    }
}
