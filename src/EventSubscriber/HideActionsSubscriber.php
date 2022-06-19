<?php

namespace App\EventSubscriber;

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
        if ($crudDto->getEntityFqcn() !== DeliveryNote::class) {
            return;
        }
        // disable action entirely delete, detail, edit
        $dn = $adminContext->getEntity()->getInstance();
        if ($dn instanceof DeliveryNote && ($dn->isDisabled() || $dn->isSigned())) {
            $crudDto->getActionsConfig()->disableActions([Action::EDIT]);
        }
        // This gives you the "configuration for all the actions".
        // Calling ->getActions() returns the array of actual actions that will be
        // enabled for the current page... so then we can modify the one for "edit"
        $actions = $crudDto->getActionsConfig()->getActions();
        if (!$editAction = $actions[Action::EDIT] ?? null) {
                return;
        }
        $editAction->setDisplayCallable(function(DeliveryNote $dn) {
            if ((!$dn->isDisabled() && $dn->isSigned()) || ($dn->isDisabled() && !$dn->isSigned())
            || ($dn->isDisabled() && $dn->isSigned()))
                return false;
            else
                return true;
        });


    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeCrudActionEvent::class => 'onBeforeCrudActionEvent',
        ];
    }
}
