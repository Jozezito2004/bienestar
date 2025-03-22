<?php
include 'conexion.php';

// Opciones de importación
$borrar_no_listados = isset($_POST['borrar_no_listados']);
$sobrescribir = isset($_POST['sobrescribir']);
$no_actualizar_historial = isset($_POST['no_actualizar_historial']);

// Lista de números de identificación procesados (para borrar los no listados)
$numeros_identificacion_procesados = [];

// Función para procesar una línea de datos
function procesarLinea($linea, $conexion, $sobrescribir, $no_actualizar_historial, &$numeros_identificacion_procesados) {
    $datos = explode(",", trim($linea));
    $tipo = $datos[0];

    if ($tipo === '##COLABORADOR##' && count($datos) >= 10) {
        $numero_identificacion = $datos[1];
        $nombre = $datos[2];
        $apellido = $datos[3];
        $fecha_nacimiento = $datos[4];
        $genero = $datos[5];
        $unidad_trabajo = $datos[6];
        $puesto = $datos[7];
        $anos_servicio = $datos[8];
        $email = $datos[9] ?: 'sin_email@buap.mx';

        // Guardar el número de identificación procesado
        $numeros_identificacion_procesados[] = $numero_identificacion;

        // Verificar si el colaborador ya existe
        $sql = "SELECT id_colaborador FROM colaboradores WHERE numero_identificacion = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $numero_identificacion);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $colaborador = $resultado->fetch_assoc();

        if ($colaborador) {
            // Si existe y se permite sobrescribir, actualizar
            if ($sobrescribir) {
                $sql = "UPDATE colaboradores SET nombre = ?, apellido = ?, fecha_nacimiento = ?, genero = ?, unidad_trabajo = ?, puesto = ?, anos_servicio = ?, email = ? WHERE numero_identificacion = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("ssssssiss", $nombre, $apellido, $fecha_nacimiento, $genero, $unidad_trabajo, $puesto, $anos_servicio, $email, $numero_identificacion);
                $stmt->execute();
            }
        } else {
            // Si no existe, insertar
            $sql = "INSERT INTO colaboradores (numero_identificacion, nombre, apellido, fecha_nacimiento, genero, unidad_trabajo, puesto, anos_servicio, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssssssis", $numero_identificacion, $nombre, $apellido, $fecha_nacimiento, $genero, $unidad_trabajo, $puesto, $anos_servicio, $email);
            $stmt->execute();
        }
        $stmt->close();
    } elseif ($tipo === '##BIOMETRICOS##' && count($datos) >= 22) {
        $numero_identificacion = $datos[1];
        $fecha_medicion = $datos[10];
        $peso = $datos[11];
        $talla = $datos[12];
        $perimetro_cintura = $datos[13];
        $porcentaje_grasa = $datos[14];
        $masa_muscular = $datos[15];
        $presion_arterial_sistolica = $datos[16];
        $presion_arterial_diastolica = $datos[17];
        $frecuencia_cardiaca = $datos[18];
        $glucosa_ayuno = $datos[19];
        $colesterol_total = $datos[20];
        $trigliceridos = $datos[21];

        // Validar que fecha_medicion no esté vacía
        if (empty($fecha_medicion)) {
            return; // Omitir esta fila si fecha_medicion está vacía
        }

        // Obtener el ID del colaborador
        $sql = "SELECT id_colaborador FROM colaboradores WHERE numero_identificacion = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $numero_identificacion);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $colaborador = $resultado->fetch_assoc();
        $stmt->close();

        if ($colaborador) {
            $id_colaborador = $colaborador['id_colaborador'];
            // Verificar si ya existe un registro biométrico para esta fecha
            $sql = "SELECT id_dato_biometrico FROM datos_biometricos WHERE id_colaborador = ? AND fecha_medicion = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("is", $id_colaborador, $fecha_medicion);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $biometrico = $resultado->fetch_assoc();

            if ($biometrico) {
                if ($sobrescribir) {
                    $sql = "UPDATE datos_biometricos SET peso = ?, talla = ?, perimetro_cintura = ?, porcentaje_grasa = ?, masa_muscular = ?, presion_arterial_sistolica = ?, presion_arterial_diastolica = ?, frecuencia_cardiaca = ?, glucosa_ayuno = ?, colesterol_total = ?, trigliceridos = ? WHERE id_colaborador = ? AND fecha_medicion = ?";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("ddddddddiiis", $peso, $talla, $perimetro_cintura, $porcentaje_grasa, $masa_muscular, $presion_arterial_sistolica, $presion_arterial_diastolica, $frecuencia_cardiaca, $glucosa_ayuno, $colesterol_total, $trigliceridos, $id_colaborador, $fecha_medicion);
                    $stmt->execute();
                }
            } else {
                $sql = "INSERT INTO datos_biometricos (id_colaborador, fecha_medicion, peso, talla, perimetro_cintura, porcentaje_grasa, masa_muscular, presion_arterial_sistolica, presion_arterial_diastolica, frecuencia_cardiaca, glucosa_ayuno, colesterol_total, trigliceridos) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("isddddddddiii", $id_colaborador, $fecha_medicion, $peso, $talla, $perimetro_cintura, $porcentaje_grasa, $masa_muscular, $presion_arterial_sistolica, $presion_arterial_diastolica, $frecuencia_cardiaca, $glucosa_ayuno, $colesterol_total, $trigliceridos);
                $stmt->execute();
            }
            $stmt->close();
        }
    } elseif ($tipo === '##HISTORIAL##' && count($datos) >= 33 && !$no_actualizar_historial) {
        $numero_identificacion = $datos[1];
        $enfermedades_diagnosticadas = $datos[22];
        $historial_medicamentos = $datos[23];
        $alergias = $datos[24];
        $cirugias_previas = $datos[25];
        $historial_familiar = $datos[26];
        $nivel_estres = $datos[27];
        $ansiedad = $datos[28];
        $depresion = $datos[29];
        $calidad_sueno_horas = $datos[30];
        $calidad_sueno_nivel = $datos[31];
        $recuperacion_fisica = $datos[32];

        // Obtener el ID del colaborador
        $sql = "SELECT id_colaborador FROM colaboradores WHERE numero_identificacion = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $numero_identificacion);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $colaborador = $resultado->fetch_assoc();
        $stmt->close();

        if ($colaborador) {
            $id_colaborador = $colaborador['id_colaborador'];
            // Verificar si ya existe un historial
            $sql = "SELECT id_historial FROM historial_salud WHERE id_colaborador = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_colaborador);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $historial = $resultado->fetch_assoc();

            if ($historial) {
                if ($sobrescribir) {
                    $sql = "UPDATE historial_salud SET enfermedades_diagnosticadas = ?, historial_medicamentos = ?, alergias = ?, cirugias_previas = ?, historial_familiar = ?, nivel_estres = ?, ansiedad = ?, depresion = ?, calidad_sueno_horas = ?, calidad_sueno_nivel = ?, recuperacion_fisica = ? WHERE id_colaborador = ?";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("sssssssiddsi", $enfermedades_diagnosticadas, $historial_medicamentos, $alergias, $cirugias_previas, $historial_familiar, $nivel_estres, $ansiedad, $depresion, $calidad_sueno_horas, $calidad_sueno_nivel, $recuperacion_fisica, $id_colaborador);
                    $stmt->execute();
                }
            } else {
                $sql = "INSERT INTO historial_salud (id_colaborador, enfermedades_diagnosticadas, historial_medicamentos, alergias, cirugias_previas, historial_familiar, nivel_estres, ansiedad, depresion, calidad_sueno_horas, calidad_sueno_nivel, recuperacion_fisica) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("issssssiddss", $id_colaborador, $enfermedades_diagnosticadas, $historial_medicamentos, $alergias, $cirugias_previas, $historial_familiar, $nivel_estres, $ansiedad, $depresion, $calidad_sueno_horas, $calidad_sueno_nivel, $recuperacion_fisica);
                $stmt->execute();
            }
            $stmt->close();
        }
    } elseif ($tipo === '##ACTIVIDAD##' && count($datos) >= 38) {
        $numero_identificacion = $datos[1];
        $fecha_registro = $datos[33];
        $promedio_pasos_diarios = $datos[34];
        $minutos_actividad_moderada = $datos[35];
        $frecuencia_entrenamiento = $datos[36];
        $deportes_practicados = $datos[37];

        // Validar que fecha_registro no esté vacía
        if (empty($fecha_registro)) {
            return; // Omitir esta fila si fecha_registro está vacía
        }

        // Obtener el ID del colaborador
        $sql = "SELECT id_colaborador FROM colaboradores WHERE numero_identificacion = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $numero_identificacion);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $colaborador = $resultado->fetch_assoc();
        $stmt->close();

        if ($colaborador) {
            $id_colaborador = $colaborador['id_colaborador'];
            // Verificar si ya existe un registro de actividad para esta fecha
            $sql = "SELECT id_actividad FROM actividad_fisica WHERE id_colaborador = ? AND fecha_registro = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("is", $id_colaborador, $fecha_registro);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $actividad = $resultado->fetch_assoc();

            if ($actividad) {
                if ($sobrescribir) {
                    $sql = "UPDATE actividad_fisica SET promedio_pasos_diarios = ?, minutos_actividad_moderada = ?, frecuencia_entrenamiento = ?, deportes_practicados = ? WHERE id_colaborador = ? AND fecha_registro = ?";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("iiisis", $promedio_pasos_diarios, $minutos_actividad_moderada, $frecuencia_entrenamiento, $deportes_practicados, $id_colaborador, $fecha_registro);
                    $stmt->execute();
                }
            } else {
                $sql = "INSERT INTO actividad_fisica (id_colaborador, fecha_registro, promedio_pasos_diarios, minutos_actividad_moderada, frecuencia_entrenamiento, deportes_practicados) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("isiiis", $id_colaborador, $fecha_registro, $promedio_pasos_diarios, $minutos_actividad_moderada, $frecuencia_entrenamiento, $deportes_practicados);
                $stmt->execute();
            }
            $stmt->close();
        }
    } elseif ($tipo === '##EXAMENES##' && count($datos) >= 49) {
        $numero_identificacion = $datos[1];
        $fecha_examen = $datos[38];
        $electrocardiograma = $datos[39];
        $prueba_esfuerzo = $datos[40];
        $perfil_lipidico = $datos[41];
        $hemoglobina_glicosilada = $datos[42];
        $creatinina = $datos[43];
        $urea = $datos[44];
        $tgo = $datos[45];
        $tgp = $datos[46];
        $densitometria_osea = $datos[47];
        $vitamina_d = $datos[48];
        $evaluacion_postura = $datos[49];

        // Validar que fecha_examen no esté vacía
        if (empty($fecha_examen)) {
            return; // Omitir esta fila si fecha_examen está vacía
        }

        // Obtener el ID del colaborador
        $sql = "SELECT id_colaborador FROM colaboradores WHERE numero_identificacion = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $numero_identificacion);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $colaborador = $resultado->fetch_assoc();
        $stmt->close();

        if ($colaborador) {
            $id_colaborador = $colaborador['id_colaborador'];
            // Verificar si ya existe un examen para esta fecha
            $sql = "SELECT id_examen FROM examenes_medicos WHERE id_colaborador = ? AND fecha_examen = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("is", $id_colaborador, $fecha_examen);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $examen = $resultado->fetch_assoc();

            if ($examen) {
                if ($sobrescribir) {
                    $sql = "UPDATE examenes_medicos SET electrocardiograma = ?, prueba_esfuerzo = ?, perfil_lipidico = ?, hemoglobina_glicosilada = ?, creatinina = ?, urea = ?, tgo = ?, tgp = ?, densitometria_osea = ?, vitamina_d = ?, evaluacion_postura = ? WHERE id_colaborador = ? AND fecha_examen = ?";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("sssdddddsdsis", $electrocardiograma, $prueba_esfuerzo, $perfil_lipidico, $hemoglobina_glicosilada, $creatinina, $urea, $tgo, $tgp, $densitometria_osea, $vitamina_d, $evaluacion_postura, $id_colaborador, $fecha_examen);
                    $stmt->execute();
                }
            } else {
                $sql = "INSERT INTO examenes_medicos (id_colaborador, fecha_examen, electrocardiograma, prueba_esfuerzo, perfil_lipidico, hemoglobina_glicosilada, creatinina, urea, tgo, tgp, densitometria_osea, vitamina_d, evaluacion_postura) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("issssdddddsds", $id_colaborador, $fecha_examen, $electrocardiograma, $prueba_esfuerzo, $perfil_lipidico, $hemoglobina_glicosilada, $creatinina, $urea, $tgo, $tgp, $densitometria_osea, $vitamina_d, $evaluacion_postura);
                $stmt->execute();
            }
            $stmt->close();
        }
    }
}

// Procesar datos pegados
if (!empty($_POST['datos_pegados'])) {
    $lineas = explode("\n", $_POST['datos_pegados']);
    foreach ($lineas as $linea) {
        if (trim($linea)) {
            procesarLinea($linea, $conexion, $sobrescribir, $no_actualizar_historial, $numeros_identificacion_procesados);
        }
    }
}

// Procesar archivo CSV
if (isset($_FILES['archivo_csv']) && $_FILES['archivo_csv']['error'] == UPLOAD_ERR_OK) {
    $csv_file = $_FILES['archivo_csv']['tmp_name'];
    if (($handle = fopen($csv_file, "r")) !== FALSE) {
        while (($datos = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $linea = implode(",", $datos);
            procesarLinea($linea, $conexion, $sobrescribir, $no_actualizar_historial, $numeros_identificacion_procesados);
        }
        fclose($handle);
    }
}

// Borrar colaboradores no listados (si la opción está seleccionada)
if ($borrar_no_listados && !empty($numeros_identificacion_procesados)) {
    $placeholders = implode(',', array_fill(0, count($numeros_identificacion_procesados), '?'));
    $sql = "DELETE FROM colaboradores WHERE numero_identificacion NOT IN ($placeholders)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($numeros_identificacion_procesados)), ...$numeros_identificacion_procesados);
    $stmt->execute();
    $stmt->close();
}

$conexion->close();
echo "Datos cargados con éxito. <a href='ver_colaboradores.php'>Ver Colaboradores</a>";
?>