<?php

class MTCPDF extends TCPDF
{

  public $dateReported = null;

  public function Footer()
  {
    $x = $this->GetX();
    $y = $this->GetY() - 15;
    $this->SetFont('freesans', null, 9, null, 'default', true);
    $this->writeHTMLCell(0, 0, null, $y, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, false, true, 'L', true);

    $this->SetFont('freesans', null, 9, null);
    $this->writeHTMLCell(0, 0, 140, null, '<strong>Fecha informe</strong>', 0, 0, false, true, 'L');
    $this->SetFont('freesans', null, 10, null);
    $this->writeHTMLCell(0, 0, $x, null, $this->dateReported, 0, 1, false, true, 'R');

    $this->writeHTML('<strong>AlerceImagen</strong> • https://' . $_SERVER["HTTP_HOST"] . '/', true, false, true, true, 'R');
  }
}
