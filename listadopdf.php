<?php
include_once 'global.php';
require('fpdf186/fpdf.php');

class PDF extends FPDF {
    public $_nombreempresa;
    public $_nombre;
    public $_nifempresa;
    public $_nif;
    public $_inicio;
    public $_fin;
    public $_altocelda;
    
    // Cabecera de página
    function Header()
    {
        // Logo
        $this->Image('img/logo.png',10,8,25, 25);
        // Arial bold 16
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Listado de registros', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);

        $this->Cell(30, $this->_altocelda, '', 0, 0, 'C');
        $this->Cell(25, $this->_altocelda, 'Empresa: ', 'LTB', 0, 'L');
        $this->Cell(50, $this->_altocelda, $this->_nombreempresa, 'RTB', 0, 'L');
        $this->Cell(25, $this->_altocelda, 'Trabajador: ', 'LTB', 0, 'L');
        $this->Cell(50, $this->_altocelda, $this->_nombre, 'RTB', 1, 'L');
        
        $this->Cell(30, $this->_altocelda, '', 0, 0, 'C');
        $this->Cell(25, $this->_altocelda, 'C.I.F./N.I.F.: ', 'LTB', 0, 'L');
        $this->Cell(50, $this->_altocelda, $this->_nifempresa, 'RTB', 0, 'L');
        $this->Cell(25, $this->_altocelda, 'N.I.F.: ', 'LTB', 0, 'L');
        $this->Cell(50, $this->_altocelda, $this->_nif, 'RTB', 1, 'L');
        
        $this->Cell(30, $this->_altocelda, '', 0, 0, 'C');
        $this->Cell(25, $this->_altocelda, 'Desde: ', 'LTB', 0, 'L');
        $this->Cell(50, $this->_altocelda, $this->_inicio, 'RTB', 0, 'L');
        $this->Cell(25, $this->_altocelda, 'Hasta: ', 'LTB', 0, 'L');
        $this->Cell(50, $this->_altocelda, $this->_fin, 'RTB', 1, 'L');
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(15, $this->_altocelda, '', 0, 1, 'C');
        $this->Cell(30, $this->_altocelda, '', 0, 0, 'C');
        $this->Cell(30, $this->_altocelda, 'Fecha', 1, 0, 'C');
        $this->Cell(30, $this->_altocelda, 'Entrada', 1, 0, 'C');
        $this->Cell(30, $this->_altocelda, 'Salida', 1, 0, 'C');
        $this->Cell(30, $this->_altocelda, 'Tiempo', 1, 1, 'C');
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
if ($_POST['listar'] != "") $busca_user = mysqli_real_escape_string($conn,  $_POST['listar']); else $busca_user = $_SESSION['user_id'];
if ($_POST['mes'] != "") $mes = mysqli_real_escape_string($conn,$_POST['mes']); else $mes = date('Y-m');
$inicio = $mes."-01";
$fin = DateTime::createFromFormat('Y-m-d', $inicio);
$fin->modify('last day of this month');
$fin = $fin->format('Y-m-d') . " 23:59:59";

// Función para generar un pdf con el listado de registros
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
        $pdf->Cell(15, $altocelda, '', 0, 1, 'C');
        $pdf->Cell(15, $altocelda, '', 0, 0, 'C');
        $pdf->Cell(30, $altocelda, 'No hay registros', 1, 1, 'C');
    } else {
        while($row = $stmt->fetch()) {
            if (!$entrada) {
                $sal = new DateTime($reg_time);
                $fecha = $sal->format('d/m/Y');
                if ($ent) {
                    $lapso = $ent->diff($sal);
                    $totalminutos += $lapso->days * 1440 + $lapso->h * 60 + $lapso->i;
                    if ($lapso->days > 0) $tiempo = $lapso->format('%d d %H:%I'); else $tiempo = $lapso->format('%H:%I');
                    $pdf->Cell(30, $altocelda, '', 0, 0, 'C');
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
        if ($pdf->GetY() > 215) $pdf->AddPage();
        $pdf->Cell(30, $altocelda, $total, 1, 1, 'C');
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
