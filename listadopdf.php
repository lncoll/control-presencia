<?php
include_once 'global.php';
require('fpdf186/fpdf.php');

class PDF extends FPDF {
    // Variables a pasar a la clase
    public $_nombreempresa;
    public $_nombre;
    public $_nifempresa;
    public $_nif;
    public $_inicio;
    public $_fin;
    public $_altocelda;
    public $_printth;
    public $_logo = "img/logo.png";

    
    // Cabecera de página
    function Header()
    {
        // Logo
        $this->Image($this->_logo,10,8,25, 25);
        // Arial bold 16
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Listado de registros', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        // Celdas con la info de empresa y trabajador
        $this->SetX(40);
        $this->Cell(25, $this->_altocelda, 'Empresa: ', 'LTB', 0, 'L');
        $this->Cell(50, $this->_altocelda, $this->_nombreempresa, 'RTB', 0, 'L');
        $this->Cell(25, $this->_altocelda, 'Trabajador: ', 'LTB', 0, 'L');
        $this->Cell(50, $this->_altocelda, $this->_nombre, 'RTB', 1, 'L');
        
        $this->SetX(40);
        $this->Cell(25, $this->_altocelda, 'C.I.F./N.I.F.: ', 'LTB', 0, 'L');
        $this->Cell(50, $this->_altocelda, $this->_nifempresa, 'RTB', 0, 'L');
        $this->Cell(25, $this->_altocelda, 'N.I.F.: ', 'LTB', 0, 'L');
        $this->Cell(50, $this->_altocelda, $this->_nif, 'RTB', 1, 'L');
        
        $this->SetX(40);
        $this->Cell(25, $this->_altocelda, 'Desde: ', 'LTB', 0, 'L');
        $this->Cell(50, $this->_altocelda, $this->_inicio, 'RTB', 0, 'L');
        $this->Cell(25, $this->_altocelda, 'Hasta: ', 'LTB', 0, 'L');
        $this->Cell(50, $this->_altocelda, $this->_fin, 'RTB', 1, 'L');
        // Cabecera de la tabla con los datos
        if ($this->_printth) {
            $this->SetFont('Arial', 'B', 10);
            $this->SetXY(40, $this->GetY() + $this->_altocelda);
            $this->Cell(30, $this->_altocelda, 'Fecha', 1, 0, 'C');
            $this->Cell(30, $this->_altocelda, 'Entrada', 1, 0, 'C');
            $this->Cell(30, $this->_altocelda, 'Salida', 1, 0, 'C');
            $this->Cell(30, $this->_altocelda, 'Tiempo', 1, 1, 'C');
        }
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10,iconv('UTF-8', 'windows-1252', "Página ").$this->PageNo().'/{nb}',0,0,'C');
    }
}

$altocelda = 5.15;
if ($_POST['pdf'] != "") $busca_user = mysqli_real_escape_string($conn,  $_POST['pdf']); else $busca_user = $_SESSION['user_id'];
if ($_POST['mes'] != "") $mes = mysqli_real_escape_string($conn,$_POST['mes']); else $mes = date('Y-m');
$inicio = $mes."-01";
$fin = DateTime::createFromFormat('Y-m-d', $inicio);
$fin->modify('last day of this month');
$fin = $fin->format('Y-m-d 23:59:59');

// Generar un pdf con el listado de registros
if (isset($_POST['pdf'])) {
    $query = "SELECT nombre, NIF FROM empleados WHERE user_id = $busca_user;";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $nombre = $row['nombre'];
    $nif = $row['NIF'];
    $result->close();

    $pdf = new PDF();
    $pdf->_nombreempresa = iconv('UTF-8', 'windows-1252', $nombreempresa);
    $pdf->_nombre = iconv('UTF-8', 'windows-1252', $nombre);
    $pdf->_nifempresa = $nifempresa;
    $pdf->_nif = $nif;
    $pdf->_inicio = $inicio;
    $pdf->_fin = substr($fin, 0, 10);
    $pdf->_altocelda = $altocelda;
    $pdf->_printth = true;
    $pdf->_logo = "img/$logo";
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',12);

    $totalminutos = 0;
    $stmt = $conn->stmt_init();
    $stmt->prepare("SELECT reg_time, entrada FROM registros WHERE reg_time BETWEEN ? AND ? AND user_id = ? ORDER BY reg_id ASC;");
    $stmt->bind_param("ssi", $inicio, $fin, $busca_user);
    $stmt->execute();
    $stmt->bind_result($reg_time, $entrada);
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        $pdf->SetY($pdf->GetY() + $altocelda);
        $pdf->SetX(40);
        $pdf->Cell(120, $altocelda, 'No hay registros', 1, 1, 'C');
    } else {
        while($row = $stmt->fetch()) {
            if (!$entrada) {
                $sal = new DateTime($reg_time);
                $fecha = $sal->format('d/m/Y');
                if ($ent) {
                    $tiempo = tiempostr($ent, $sal);
                    $lapso = $ent->diff($sal);
                    $minutos = $lapso->days * 1440 + $lapso->h * 60 + $lapso->i;
                    $minutos -= $minutos % $bloquetiempo;
                    $totalminutos += $minutos;
/*                    if ($lapso->days > 0) {
                        $tiempo = sprintf("%dd %02d:%02d", floor($minutos/1440), floor($minutos/60), $minutos % 60);
                    } else {
                        $tiempo = sprintf("%02d:%02d", floor($minutos/60), $minutos % 60);
                    } */
                    $pdf->SetX(40); // Cell(30, $altocelda, '', 0, 0, 'C');
                    $pdf->Cell(30, $altocelda, $fecha, 1, 0, 'C');
                    $pdf->Cell(30, $altocelda, $ent->format('H:i'), 1, 0, 'C');
                    $pdf->Cell(30, $altocelda, $sal->format('H:i'), 1, 0, 'C');
                    $pdf->Cell(30, $altocelda, $tiempo, 1, 1, 'C');
                } else {
                    $pdf->Cell(15, $altocelda, '', 0, 0, 'C');
                    $pdf->Cell(30, $altocelda, $fecha, 1, 0, 'C');
                    $pdf->Cell(30, $altocelda, $ent->format('H:i'), 1, 0, 'C');
                    $pdf->Cell(30, $altocelda, '---', 1, 0, 'C');
                    $pdf->Cell(30, $altocelda, '---', 1, 1, 'C');
                }
            } else {
                $ent = new DateTime($reg_time);
            }
        }
        $total = sprintf("%d:%02d", floor($totalminutos / 60), $totalminutos % 60);
        $pdf->Cell(30, $altocelda, '', 0, 1, 'C');
        $pdf->SetX(100);
        $pdf->Cell(30, $altocelda, 'Tiempo total:', 1, 0, 'C');
        $pdf->Cell(30, $altocelda, $total, 1, 1, 'C');
        $pdf->_printth = false;
        if ($pdf->GetY() > 225) $pdf->AddPage();

        $pdf->Cell(30, $altocelda, '', 0, 1, 'C');
        $pdf->SetX(40);  $pdf->Write($altocelda, 'Por la empresa:');
        $pdf->SetX(120); $pdf->Write($altocelda, "Por el trabajador:\n");
        $pdf->SetX(40);  $pdf->Write($altocelda, 'Firma:');
        $pdf->SetX(120); $pdf->Write($altocelda, "Firma:\n");
        $pdf->Cell(30, 30, '', 0, 1, 'C');
        $pdf->SetX(40);  $pdf->Write($altocelda, 'Fecha:');
        $pdf->SetX(120); $pdf->Write($altocelda, "Fecha:\n");
    }
    $stmt->close();
    $pdf->Output();
}
