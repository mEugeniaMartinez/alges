<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Business;
use App\Entity\Client;
use App\Entity\DeliveryNote;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

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

        /*$userRepo = $this->em->getRepository(User::class);
        $userDB = $userRepo->find(26);
        $clientRepo = $this->em->getRepository(Client::class);
        $clientDB = $clientRepo->find(27);
        $date = date_create_from_format('d-m-Y', date("d-m-Y"));

        $dn = new DeliveryNote($userDB);
        $dn->setClient($clientDB);
        $dn->setDate($date);
        $this->em->persist($dn);
        $this->em->flush();

        $dn->setNumber($dn->getId());
        $this->em->flush();

        $dns = $userDB->getDeliveryNotes();
        foreach ($dns as $d) {
            var_dump($d->getNumber());
            var_dump($d->getDate()->format('d-m-Y'));
        }*/

        return $this->render('base.html.twig', [
            'controller_name' => 'BaseController',
            //clients' => $clientDB,
        ]);
    }
}
