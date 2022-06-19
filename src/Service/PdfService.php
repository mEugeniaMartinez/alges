<?php

    namespace App\Service;

    use App\Entity\DeliveryNote;
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

        public function renderPdf(?DeliveryNote $dn): void
        {
            $html = $this->twig->render('pdf/pdf-template.html.twig', array('dn' => $dn));
            $this->domPdf->loadHtml($html);
            $this->domPdf->render();
        }

        public function showPdfFile(?DeliveryNote $dn): void
        {
            $this->renderPdf($dn);
            $filename = sprintf("albaran_%d", $dn->getId());
            $this->domPdf->stream($filename, array('Attachment' => 0));
        }

        public function getPdf(?DeliveryNote $dn): ?string
        {
            $this->renderPdf($dn);
            return $this->domPdf->output();
            /*file_put_contents('uploads/pdf/' . $filename . '.pdf', $content);*/
        }

    }