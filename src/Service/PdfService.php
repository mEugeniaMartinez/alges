<?php

    namespace App\Service;

    use App\Entity\DeliveryNote;
    use Doctrine\ORM\EntityManagerInterface;
    use Dompdf\Dompdf;
    use Dompdf\Options;
    use Twig\Environment;

    class PdfService
    {
        private $domPdf;
        private $em;
        private Environment $twig;

        public function __construct(EntityManagerInterface $entityManager, Environment $twig)
        {
            $this->em = $entityManager;

            $this->domPdf = new Dompdf();
            /*$context = stream_context_create([
                'ssl' => [
                    'verify_peer' => FALSE,
                    'verify_peer_name' => FALSE,
                    'allow_self_signed'=> TRUE
                ]
            ]);
            $this->domPdf->setHttpContext($context);*/
            $pdfOptions = new Options();
            //$pdfOptions->set('defaultFont', 'Sans Serif');
            $pdfOptions->set('defaultPaperSize', 'A4');
        /*    $pdfOptions->set('tempDir', 'temp');*/
            /*$pdfOptions->set('isRemoteEnable', true);*/
            $this->domPdf->setOptions($pdfOptions);
            $this->twig = $twig;
        }


        public function showPdfFile(/*$html,*/ $dnId)
        {
            $dn = $this->em->getRepository(DeliveryNote::class)
            ->find($dnId);
            $html = $this->twig->render('pdf/pdf-template.html.twig', array('dn' => $dn));
            $this->domPdf->loadHtml($html);
            $this->domPdf->render();
            $filename = sprintf("albaran_%d", $dnId);
            $content = $this->domPdf->output();
            file_put_contents('uploads/pdf/'. $filename . '.pdf', $content);
            $this->domPdf->stream($filename);
        }

    }