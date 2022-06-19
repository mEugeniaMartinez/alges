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
        private Environment $twig;

        public function __construct(Environment $twig)
        {
            $this->domPdf = new Dompdf();
            $pdfOptions = new Options();
            $pdfOptions->set('defaultPaperSize', 'A4');
            $this->domPdf->setOptions($pdfOptions);
            $this->twig = $twig;
        }


        public function showPdfFile($dn, EntityManagerInterface $em)
        {
            $html = $this->twig->render('pdf/pdf-template.html.twig', array('dn' => $dn));
            $this->domPdf->loadHtml($html);
            $this->domPdf->render();
            $filename = sprintf("albaran_%d", $dn->getId());
            /*$content = $this->domPdf->output();
            file_put_contents('uploads/pdf/' . $filename . '.pdf', $content);*/
            $this->domPdf->stream($filename, array('Attachment' => 0));

        }

    }