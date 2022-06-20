<?php

    namespace App\Repository;

    use App\Entity\DeliveryNote;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;

    /**
     * @extends ServiceEntityRepository<DeliveryNote>
     *
     * @method DeliveryNote|null find($id, $lockMode = null, $lockVersion = null)
     * @method DeliveryNote|null findOneBy(array $criteria, array $orderBy = null)
     * @method DeliveryNote[]    findAll()
     * @method DeliveryNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
     */
    class DeliveryNoteRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, DeliveryNote::class);
        }

        public function add(DeliveryNote $entity, bool $flush = false): void
        {
            $this->getEntityManager()->persist($entity);

            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

        public function update(DeliveryNote $entity): void
        {

            $this->getEntityManager()->merge($entity);
            $this->getEntityManager()->flush();

        }

        public function remove(DeliveryNote $entity, bool $flush = false): void
        {
            $this->getEntityManager()->remove($entity);

            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

        public function updateCompleted(DeliveryNote $entity): void
        {
            if ($entity->getClient() !== null && $entity->getDate() !== null && $entity->getIntervention()) {
                $entity->setCompleted(true);
            } else {
                $entity->setCompleted(false);
            }
            $this->update($entity);
        }

        public function updatePdf(DeliveryNote $entity, $pdf)
        {
            $entity->setPdf($pdf);
            $this->update($entity);
        }
    }
