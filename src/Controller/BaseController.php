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

        $userRepo = $this->em->getRepository(User::class);
        $userDB = $userRepo->find(26);
        $dnRepo = $this->em->getRepository(DeliveryNote::class);
        $dnDB = $dnRepo->find(1);
        $date = date_create_from_format('Y-m-d', '2022-06-30');
        $dnDB->setDate($date);
        $this->em->flush();

        /*$dn = new DeliveryNote("notSigned", $userDB);
        $dn->setClient($clientDB);
        $dn->getDate(date("Y-m-d"));

        $this->em->persist($dn);
        $this->em->flush();*/

        $dns = $userDB->getDeliveryNotes();
        foreach ($dns as $d) {
            var_dump($d->getClient()->getEmail());
            var_dump($d->getDate()->format('d-m-Y'));
        }

        return $this->render('base.html.twig', [
            'controller_name' => 'BaseController',
            //clients' => $clientDB,
        ]);
    }
}
