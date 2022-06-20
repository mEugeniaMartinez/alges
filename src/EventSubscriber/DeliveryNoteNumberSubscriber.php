<?php

    namespace App\EventSubscriber;

    use App\Entity\DeliveryNote;
    use Doctrine\ORM\EntityManagerInterface;
    use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;

    class DeliveryNoteNumberSubscriber implements EventSubscriberInterface
    {
        private EntityManagerInterface $em;

        public function __construct(EntityManagerInterface $entityManager)
        {
            $this->em = $entityManager;
        }

        public function onAfterEntityPersistedEvent(AfterEntityPersistedEvent $event): void
        {
            $dn = $event->getEntityInstance();
            if (!$dn instanceof DeliveryNote) {
                return;
            }

            $dn->generateNumber();
            $this->em->getRepository(DeliveryNote::class)->update($dn);
        }

        public static function getSubscribedEvents(): array
        {
            return [
                AfterEntityPersistedEvent::class => 'onAfterEntityPersistedEvent',
            ];
        }

    }
