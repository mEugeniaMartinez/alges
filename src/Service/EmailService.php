<?php

    namespace App\Service;

    use App\Entity\DeliveryNote;
    use Symfony\Component\Mailer\MailerInterface;
    use Symfony\Component\Mime\Email;
    use Twig\Environment;

    class EmailService
    {
        private MailerInterface $mailer;
        private PdfService $pdfService;

        public function __construct(MailerInterface $mailer, PdfService $pdfService, Environment $twig)
        {
            $this->mailer = $mailer;
            $this->pdfService = new PdfService($twig);
        }

        //TODO - Cambiar config para enviar a emails reales!
        public function sendPdfByEmail(?DeliveryNote $dn)
        {
            $user = $dn->getUser();
            $client = $dn->getClient();
            $pdf = $this->pdfService->getPdf($dn);

            $email = (new Email())
                ->from($user->getEmail())
                ->to($client->getEmail())
                ->subject('Envío de albarán - ' . $user->getName())
                ->text($user->getEmailText())
                ->attach($pdf, sprintf('albaran_%s.pdf', $user->getName()));

            $this->mailer->send($email);
        }

    }