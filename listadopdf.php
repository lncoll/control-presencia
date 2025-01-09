<?php
include_once 'global.php';
require('fpdf186/fpdf.php');

$altocelda = 5.15;

class PDF extends FPDF {
    // Cabecera de página
    function Header()
    {
        global $nombre, $nif, $inicio, $fin, $altocelda;
        // Logo
        $this->Image('img/logo.png',10,8,33);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Listado de registros', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Empleado: ' . $nombre.". NIF: ".$nif, 0, 1, 'C');
        $this->Cell(0, 10, 'Desde: ' . $inicio . ' Hasta: ' . $fin, 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(15, $altocelda, '', 0, 1, 'C');
        $this->Cell(15, $altocelda, '', 0, 0, 'C');
        $this->Cell(30, $altocelda, 'Fecha', 1, 0, 'C');
        $this->Cell(30, $altocelda, 'Entrada', 1, 0, 'C');
        $this->Cell(30, $altocelda, 'Salida', 1, 0, 'C');
        $this->Cell(30, $altocelda, 'Tiempo', 1, 1, 'C');
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10,"P'agina ".$this->PageNo().'/{nb}',0,0,'C');
    }
}

if ($_POST['listar'] != "") $busca_user = mysqli_real_escape_string($conn,  $_POST['listar']); else $busca_user = $_SESSION['user_id'];
if ($_POST['inicio'] != "") $inicio = mysqli_real_escape_string($conn,$_POST['inicio']); else $inicio = date('Y-m-01');
if ($_POST['fin'] != "") $fin = mysqli_real_escape_string($conn,$_POST['fin']." 23:59:59"); else $fin = date('Y-m-t')." 23:59:59";

// Función para generar un pdf con el listado de registros
if (isset($_POST['pdf'])) {
    $query = "SELECT nombre, NIF FROM empleados WHERE user_id = $busca_user;";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $nombre = $row['nombre'];
    $nif = $row['NIF'];
    $result->close();

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Times','',12);
    $stmt = $conn->stmt_init();
    $stmt->prepare("SELECT reg_time, entrada FROM registros WHERE reg_time BETWEEN ? AND ? AND user_id = ? ORDER BY reg_id ASC;");
    $stmt->bind_param("ssi", $inicio, $fin, $busca_user);
    $stmt->execute();
    $stmt->bind_result($reg_time, $entrada);
    $stmt->store_result();
    while($row = $stmt->fetch()) {
        if (!$entrada) {
            $sal = new DateTime($reg_time);
            $fecha = $sal->format('d/m/Y');
            if ($ent) {
                $lapso = $ent->diff($sal);
                if ($lapso->days > 0) $tiempo = $lapso->format('%d d %H:%I'); else $tiempo = $lapso->format('%H:%I');
                $pdf->Cell(15, $altocelda, '', 0, 0, 'C');
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
    $stmt->close();
    $pdf->Output();
}
