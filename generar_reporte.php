<?php
// Incluir la librería TCPDF
require('tcpdf/tcpdf.php');

// Incluir la conexión a la base de datos
include 'conexion.php';

// Crear una clase que extienda TCPDF para personalizar el encabezado y pie de página
class PDF extends TCPDF {
    public function Header() {
        // Título del reporte
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 10, 'Reporte de Bienestar BUAP', 0, 1, 'C');
        $this->Ln(5);
        // Línea separadora
        $this->SetLineWidth(0.5);
        $this->Line(10, 20, 200, 20);
        $this->Ln(5);
    }

    public function Footer() {
        // Posición a 1.5 cm del final de la página
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
        // Fecha de generación
        $this->SetY(-15);
        $this->SetX(-40);
        $this->Cell(0, 10, 'Generado el: ' . date('Y-m-d'), 0, 0, 'R');
    }

    // Método para agregar una sección con título
    public function SectionTitle($title) {
        $this->SetFont('helvetica', 'B', 12);
        $this->SetFillColor(200, 220, 255);
        $this->Cell(0, 8, $title, 0, 1, 'L', true);
        $this->Ln(2);
    }

    // Método para agregar un campo de datos
    public function DataField($label, $value) {
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(50, 6, $label . ':', 0, 0);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 6, $value, 0, 1);
    }
}

// Crear el objeto PDF
$pdf = new PDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Bienestar BUAP');
$pdf->SetTitle('Reporte de Bienestar BUAP');
$pdf->SetMargins(10, 30, 10);
$pdf->AddPage();

// Obtener los datos de los colaboradores
$sql = "SELECT * FROM colaboradores ORDER BY apellido, nombre";
$resultado = $conexion->query($sql);

// Almacenar todos los colaboradores en un arreglo
$colaboradores = [];
while ($row = $resultado->fetch_assoc()) {
    $colaboradores[] = $row;
}

// Contar el número total de colaboradores
$total_colaboradores = count($colaboradores);

// Iterar sobre los colaboradores
foreach ($colaboradores as $index => $colaborador) {
    $id_colaborador = $colaborador['id_colaborador'];

    // Sección: Datos Personales
    $pdf->SectionTitle('Colaborador: ' . $colaborador['nombre'] . ' ' . $colaborador['apellido']);
    $pdf->DataField('Número de Identificación', $colaborador['numero_identificacion']);
    $pdf->DataField('Fecha de Nacimiento', $colaborador['fecha_nacimiento']);
    $pdf->DataField('Edad', floor((time() - strtotime($colaborador['fecha_nacimiento'])) / 31556926));
    $pdf->DataField('Género', $colaborador['genero']);
    $pdf->DataField('Unidad de Trabajo', $colaborador['unidad_trabajo']);
    $pdf->DataField('Puesto', $colaborador['puesto']);
    $pdf->DataField('Años de Servicio', $colaborador['anos_servicio']);
    $pdf->DataField('Email', $colaborador['email']);
    $pdf->Ln(5);

    // Sección: Datos Biométricos
    $sql_biometricos = "SELECT * FROM datos_biometricos WHERE id_colaborador = ? ORDER BY fecha_medicion DESC LIMIT 1";
    $stmt = $conexion->prepare($sql_biometricos);
    $stmt->bind_param("i", $id_colaborador);
    $stmt->execute();
    $resultado_biometricos = $stmt->get_result();
    if ($biometrico = $resultado_biometricos->fetch_assoc()) {
        $pdf->SectionTitle('Datos Biométricos');
        $pdf->DataField('Fecha de Medición', $biometrico['fecha_medicion']);
        $pdf->DataField('Peso (kg)', $biometrico['peso']);
        $pdf->DataField('Talla (cm)', $biometrico['talla']);
        $pdf->DataField('Perímetro de Cintura (cm)', $biometrico['perimetro_cintura']);
        $pdf->DataField('Porcentaje de Grasa (%)', $biometrico['porcentaje_grasa']);
        $pdf->DataField('Masa Muscular (kg)', $biometrico['masa_muscular']);
        $pdf->DataField('Presión Arterial (mmHg)', $biometrico['presion_arterial_sistolica'] . '/' . $biometrico['presion_arterial_diastolica']);
        $pdf->DataField('Frecuencia Cardíaca (lpm)', $biometrico['frecuencia_cardiaca']);
        $pdf->DataField('Glucosa en Ayuno (mg/dL)', $biometrico['glucosa_ayuno']);
        $pdf->DataField('Colesterol Total (mg/dL)', $biometrico['colesterol_total']);
        $pdf->DataField('Triglicéridos (mg/dL)', $biometrico['trigliceridos']);
        $pdf->Ln(5);
    }
    $stmt->close();

    // Sección: Historial de Salud
    $sql_historial = "SELECT * FROM historial_salud WHERE id_colaborador = ?";
    $stmt = $conexion->prepare($sql_historial);
    $stmt->bind_param("i", $id_colaborador);
    $stmt->execute();
    $resultado_historial = $stmt->get_result();
    if ($historial = $resultado_historial->fetch_assoc()) {
        $pdf->SectionTitle('Historial de Salud');
        $pdf->DataField('Enfermedades Diagnosticadas', $historial['enfermedades_diagnosticadas']);
        $pdf->DataField('Historial de Medicamentos', $historial['historial_medicamentos']);
        $pdf->DataField('Alergias', $historial['alergias']);
        $pdf->DataField('Cirugías Previas', $historial['cirugias_previas']);
        $pdf->DataField('Historial Familiar', $historial['historial_familiar']);
        $pdf->DataField('Nivel de Estrés', $historial['nivel_estres']);
        $pdf->DataField('Ansiedad (1-10)', $historial['ansiedad']);
        $pdf->DataField('Depresión (1-10)', $historial['depresion']);
        $pdf->DataField('Horas Promedio de Sueño', $historial['calidad_sueno_horas']);
        $pdf->DataField('Calidad del Sueño', $historial['calidad_sueno_nivel']);
        $pdf->DataField('Recuperación Física', $historial['recuperacion_fisica']);
        $pdf->Ln(5);
    }
    $stmt->close();

    // Sección: Actividad Física
    $sql_actividad = "SELECT * FROM actividad_fisica WHERE id_colaborador = ? ORDER BY fecha_registro DESC LIMIT 1";
    $stmt = $conexion->prepare($sql_actividad);
    $stmt->bind_param("i", $id_colaborador);
    $stmt->execute();
    $resultado_actividad = $stmt->get_result();
    if ($actividad = $resultado_actividad->fetch_assoc()) {
        $pdf->SectionTitle('Actividad Física');
        $pdf->DataField('Fecha de Registro', $actividad['fecha_registro']);
        $pdf->DataField('Promedio de Pasos Diarios', $actividad['promedio_pasos_diarios']);
        $pdf->DataField('Minutos de Actividad Moderada', $actividad['minutos_actividad_moderada']);
        $pdf->DataField('Frecuencia de Entrenamiento (días/semana)', $actividad['frecuencia_entrenamiento']);
        $pdf->DataField('Deportes Practicados', $actividad['deportes_practicados']);
        $pdf->Ln(5);
    }
    $stmt->close();

    // Sección: Exámenes Médicos
    $sql_examenes = "SELECT * FROM examenes_medicos WHERE id_colaborador = ? ORDER BY fecha_examen DESC LIMIT 1";
    $stmt = $conexion->prepare($sql_examenes);
    $stmt->bind_param("i", $id_colaborador);
    $stmt->execute();
    $resultado_examenes = $stmt->get_result();
    if ($examen = $resultado_examenes->fetch_assoc()) {
        $pdf->SectionTitle('Exámenes Médicos');
        $pdf->DataField('Fecha de Examen', $examen['fecha_examen']);
        $pdf->DataField('Electrocardiograma', $examen['electrocardiograma']);
        $pdf->DataField('Prueba de Esfuerzo', $examen['prueba_esfuerzo']);
        $pdf->DataField('Perfil Lipídico', $examen['perfil_lipidico']);
        $pdf->DataField('Hemoglobina Glicosilada (%)', $examen['hemoglobina_glicosilada']);
        $pdf->DataField('Creatinina (mg/dL)', $examen['creatinina']);
        $pdf->DataField('Urea (mg/dL)', $examen['urea']);
        $pdf->DataField('TGO (U/L)', $examen['tgo']);
        $pdf->DataField('TGP (U/L)', $examen['tgp']);
        $pdf->DataField('Densitometría Ósea', $examen['densitometria_osea']);
        $pdf->DataField('Vitamina D (ng/mL)', $examen['vitamina_d']);
        $pdf->DataField('Evaluación de Postura', $examen['evaluacion_postura']);
        $pdf->Ln(5);
    }
    $stmt->close();

    // Agregar un salto de página para el siguiente colaborador (excepto en el último)
    if ($index < $total_colaboradores - 1) {
        $pdf->AddPage();
    }
}

$conexion->close();

// Generar el PDF
$pdf->Output('Reporte_Bienestar_BUAP_' . date('Ymd') . '.pdf', 'D'); // Corregido: nombre primero, destino después
?>