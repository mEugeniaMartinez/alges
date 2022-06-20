<?php

    namespace App\EventSubscriber;

    use App\Entity\Client;
    use App\Entity\DeliveryNote;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;

    class HideActionsSubscriber implements EventSubscriberInterface
    {
        public function onBeforeCrudActionEvent(BeforeCrudActionEvent $event): void
        {
            if (!$adminContext = $event->getAdminContext()) {
                return;
            }
            if (!$crudDto = $adminContext->getCrud()) {
                return;
            }
            $this->hideDNActions($adminContext, $crudDto);
            $this->hideClientActions($adminContext, $crudDto);
        }

        public function hideDNActions($adminContext, $crudDto)
        {
            if ($crudDto->getEntityFqcn() !== DeliveryNote::class) {
                return;
            }

            $dn = $adminContext->getEntity()->getInstance();
            if ($dn instanceof DeliveryNote && ($dn->isDisabled() || $dn->isSigned())) {
                $crudDto->getActionsConfig()->disableActions([Action::EDIT]);
            }
            $actions = $crudDto->getActionsConfig()->getActions();
            if (!$editAction = $actions[Action::EDIT] ?? null) {
                return;
            }
            $editAction->setDisplayCallable(function (DeliveryNote $dn) {
                return (!$dn->isDisabled() && $dn->isSigned()) || ($dn->isDisabled() && !$dn->isSigned())
                || ($dn->isDisabled() && $dn->isSigned()) ? false : true;
            });
        }

        public function hideClientActions($adminContext, $crudDto)
        {
            if ($crudDto->getEntityFqcn() !== Client::class) {
                return;
            }

            $client = $adminContext->getEntity()->getInstance();
            if ($client instanceof Client && !$client->getDeliveryNotes()->isEmpty()) {
                $crudDto->getActionsConfig()->disableActions([Action::DELETE]);
            }
            $actions = $crudDto->getActionsConfig()->getActions();
            if (!$editAction = $actions[Action::DELETE] ?? null) {
                return;
            }
            $editAction->setDisplayCallable(function (Client $client) {
                return !$client->getDeliveryNotes()->isEmpty() ? false : true;
            });
        }

        public static function getSubscribedEvents(): array
        {
            return [
                BeforeCrudActionEvent::class => 'onBeforeCrudActionEvent',
            ];
        }
    }
