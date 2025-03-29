<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Iniciar una transacción para asegurar consistencia
    $conexion->begin_transaction();

    try {
        // 1. Insertar en la tabla colaboradores
        $numero_identificacion = $_POST['numero_identificacion'];
        $apellido = $_POST['apellido'];
        $nombre = $_POST['nombre'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $genero = $_POST['genero'];
        $unidad_trabajo = $_POST['unidad_trabajo'];
        $puesto = $_POST['puesto'];
        $anos_servicio = $_POST['anos_servicio'];
        $email = $_POST['email'] ?: null;

        $stmt = $conexion->prepare("INSERT INTO colaboradores (numero_identificacion, apellido, nombre, fecha_nacimiento, genero, unidad_trabajo, puesto, anos_servicio, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssiss", $numero_identificacion, $apellido, $nombre, $fecha_nacimiento, $genero, $unidad_trabajo, $puesto, $anos_servicio, $email);
        $stmt->execute();
        $id_colaborador = $conexion->insert_id;
        $stmt->close();

        // 2. Insertar en la tabla datos_biometricos
        $fecha_medicion = $_POST['fecha_medicion'];
        $peso = $_POST['peso'];
        $talla = $_POST['talla'];
        $perimetro_cintura = $_POST['perimetro_cintura'] ?: null;
        $porcentaje_grasa = $_POST['porcentaje_grasa'] ?: null;
        $masa_muscular = $_POST['masa_muscular'] ?: null;
        $presion_arterial_sistolica = $_POST['presion_arterial_sistolica'] ?: null;
        $presion_arterial_diastolica = $_POST['presion_arterial_diastolica'] ?: null;
        $frecuencia_cardiaca = $_POST['frecuencia_cardiaca'] ?: null;
        $glucosa_ayuno = $_POST['glucosa_ayuno'] ?: null;
        $colesterol_total = $_POST['colesterol_total'] ?: null;
        $trigliceridos = $_POST['trigliceridos'] ?: null;

        $stmt = $conexion->prepare("INSERT INTO datos_biometricos (id_colaborador, fecha_medicion, peso, talla, perimetro_cintura, porcentaje_grasa, masa_muscular, presion_arterial_sistolica, presion_arterial_diastolica, frecuencia_cardiaca, glucosa_ayuno, colesterol_total, trigliceridos) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isddddddddddd", $id_colaborador, $fecha_medicion, $peso, $talla, $perimetro_cintura, $porcentaje_grasa, $masa_muscular, $presion_arterial_sistolica, $presion_arterial_diastolica, $frecuencia_cardiaca, $glucosa_ayuno, $colesterol_total, $trigliceridos);
        $stmt->execute();
        $stmt->close();

        // 3. Insertar en la tabla historial_salud
        $enfermedades_diagnosticadas = $_POST['enfermedades_diagnosticadas'] ?: null;
        $historial_medicamentos = $_POST['historial_medicamentos'] ?: null;
        $alergias = $_POST['alergias'] ?: null;
        $cirugias_previas = $_POST['cirugias_previas'] ?: null;
        $historial_familiar = $_POST['historial_familiar'] ?: null;
        $nivel_estres = $_POST['nivel_estres'] ?: null;
        $ansiedad = $_POST['ansiedad'] ?: null;
        $depresion = $_POST['depresion'] ?: null;
        $calidad_sueno_horas = $_POST['calidad_sueno_horas'] ?: null;
        $calidad_sueno_nivel = $_POST['calidad_sueno_nivel'] ?: null;
        $recuperacion_fisica = $_POST['recuperacion_fisica'] ?: null;

        $stmt = $conexion->prepare("INSERT INTO historial_salud (id_colaborador, enfermedades_diagnosticadas, historial_medicamentos, alergias, cirugias_previas, historial_familiar, nivel_estres, ansiedad, depresion, calidad_sueno_horas, calidad_sueno_nivel, recuperacion_fisica) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssiiddss", $id_colaborador, $enfermedades_diagnosticadas, $historial_medicamentos, $alergias, $cirugias_previas, $historial_familiar, $nivel_estres, $ansiedad, $depresion, $calidad_sueno_horas, $calidad_sueno_nivel, $recuperacion_fisica);
        $stmt->execute();
        $stmt->close();

        // 4. Insertar en la tabla actividad_fisica
        $fecha_registro_actividad = $_POST['fecha_registro_actividad'];
        $promedio_pasos_diarios = $_POST['promedio_pasos_diarios'] ?: null;
        $minutos_actividad_moderada = $_POST['minutos_actividad_moderada'] ?: null;
        $frecuencia_entrenamiento = $_POST['frecuencia_entrenamiento'] ?: null;
        $deportes_practicados = $_POST['deportes_practicados'] ?: null;

        $stmt = $conexion->prepare("INSERT INTO actividad_fisica (id_colaborador, fecha_registro, promedio_pasos_diarios, minutos_actividad_moderada, frecuencia_entrenamiento, deportes_practicados) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isiiis", $id_colaborador, $fecha_registro_actividad, $promedio_pasos_diarios, $minutos_actividad_moderada, $frecuencia_entrenamiento, $deportes_practicados);
        $stmt->execute();
        $stmt->close();

        // 5. Insertar en la tabla examenes_medicos
        $fecha_examen = $_POST['fecha_examen'];
        $electrocardiograma = $_POST['electrocardiograma'] ?: null;
        $prueba_esfuerzo = $_POST['prueba_esfuerzo'] ?: null;
        $perfil_lipidico = $_POST['perfil_lipidico'] ?: null;
        $hemoglobina_glicosilada = $_POST['hemoglobina_glicosilada'] ?: null;
        $creatinina = $_POST['creatinina'] ?: null;
        $urea = $_POST['urea'] ?: null;
        $tgo = $_POST['tgo'] ?: null;
        $tgp = $_POST['tgp'] ?: null;
        $densitometria_osea = $_POST['densitometria_osea'] ?: null;
        $vitamina_d = $_POST['vitamina_d'] ?: null;
        $evaluacion_postura = $_POST['evaluacion_postura'] ?: null;

        $stmt = $conexion->prepare("INSERT INTO examenes_medicos (id_colaborador, fecha_examen, electrocardiograma, prueba_esfuerzo, perfil_lipidico, hemoglobina_glicosilada, creatinina, urea, tgo, tgp, densitometria_osea, vitamina_d, evaluacion_postura) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssdddddsds", $id_colaborador, $fecha_examen, $electrocardiograma, $prueba_esfuerzo, $perfil_lipidico, $hemoglobina_glicosilada, $creatinina, $urea, $tgo, $tgp, $densitometria_osea, $vitamina_d, $evaluacion_postura);
        $stmt->execute();
        $stmt->close();

        // Confirmar la transacción
        $conexion->commit();

        // Redirigir con mensaje de éxito
        header("Location: agregar_colaborador.php?mensaje=Colaborador registrado con éxito");
        exit();
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conexion->rollback();
        // Redirigir con mensaje de error
        header("Location: agregar_colaborador.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Si no es POST, redirigir al formulario
    header("Location: agregar_colaborador.php");
    exit();
}

$conexion->close();
?>