<?php
include 'header.php';
include 'navbar.php';
?>

<div class="container mt-4">
    <h1 class="text-center" style="color: #003087;">Agregar Nuevo Colaborador</h1>
    <div class="card p-4" style="background-color: #F4F4F4; border: 2px solid #003087;">
        <form action="procesar_agregar_colaborador.php" method="POST">
            <!-- Datos Personales -->
            <h3>Datos Personales</h3>
            <div class="mb-3">
                <label for="numero_identificacion" class="form-label">Número de Identificación</label>
                <input type="text" class="form-control" name="numero_identificacion" required>
            </div>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" name="apellido" required>
            </div>
            <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" name="fecha_nacimiento" required>
            </div>
            <div class="mb-3">
                <label for="genero" class="form-label">Género</label>
                <select class="form-select" name="genero" required>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="unidad_trabajo" class="form-label">Unidad de Trabajo</label>
                <input type="text" class="form-control" name="unidad_trabajo" required>
            </div>
            <div class="mb-3">
                <label for="puesto" class="form-label">Puesto</label>
                <input type="text" class="form-control" name="puesto" required>
            </div>
            <div class="mb-3">
                <label for="anos_servicio" class="form-label">Años de Servicio</label>
                <input type="number" class="form-control" name="anos_servicio" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email (opcional)</label>
                <input type="email" class="form-control" name="email">
            </div>

            <!-- Datos Biométricos -->
            <h3>Datos Biométricos</h3>
            <div class="mb-3">
                <label for="fecha_medicion" class="form-label">Fecha de Medición</label>
                <input type="date" class="form-control" name="fecha_medicion" required>
            </div>
            <div class="mb-3">
                <label for="peso" class="form-label">Peso (kg)</label>
                <input type="number" step="0.1" class="form-control" name="peso" required>
            </div>
            <div class="mb-3">
                <label for="talla" class="form-label">Talla (cm)</label>
                <input type="number" step="0.1" class="form-control" name="talla" required>
            </div>
            <div class="mb-3">
                <label for="perimetro_cintura" class="form-label">Perímetro de Cintura (cm, opcional)</label>
                <input type="number" step="0.1" class="form-control" name="perimetro_cintura">
            </div>
            <div class="mb-3">
                <label for="porcentaje_grasa" class="form-label">Porcentaje de Grasa (%, opcional)</label>
                <input type="number" step="0.1" class="form-control" name="porcentaje_grasa">
            </div>
            <div class="mb-3">
                <label for="masa_muscular" class="form-label">Masa Muscular (kg, opcional)</label>
                <input type="number" step="0.1" class="form-control" name="masa_muscular">
            </div>
            <div class="mb-3">
                <label for="presion_arterial_sistolica" class="form-label">Presión Arterial Sistólica (mmHg, opcional)</label>
                <input type="number" class="form-control" name="presion_arterial_sistolica">
            </div>
            <div class="mb-3">
                <label for="presion_arterial_diastolica" class="form-label">Presión Arterial Diastólica (mmHg, opcional)</label>
                <input type="number" class="form-control" name="presion_arterial_diastolica">
            </div>
            <div class="mb-3">
                <label for="frecuencia_cardiaca" class="form-label">Frecuencia Cardíaca (lpm, opcional)</label>
                <input type="number" class="form-control" name="frecuencia_cardiaca">
            </div>
            <div class="mb-3">
                <label for="glucosa_ayuno" class="form-label">Glucosa en Ayuno (mg/dL, opcional)</label>
                <input type="number" class="form-control" name="glucosa_ayuno">
            </div>
            <div class="mb-3">
                <label for="colesterol_total" class="form-label">Colesterol Total (mg/dL, opcional)</label>
                <input type="number" class="form-control" name="colesterol_total">
            </div>
            <div class="mb-3">
                <label for="trigliceridos" class="form-label">Triglicéridos (mg/dL, opcional)</label>
                <input type="number" class="form-control" name="trigliceridos">
            </div>

            <!-- Historial de Salud -->
            <h3>Historial de Salud</h3>
            <div class="mb-3">
                <label for="enfermedades_diagnosticadas" class="form-label">Enfermedades Diagnosticadas (opcional)</label>
                <textarea class="form-control" name="enfermedades_diagnosticadas"></textarea>
            </div>
            <div class="mb-3">
                <label for="historial_medicamentos" class="form-label">Historial de Medicamentos (opcional)</label>
                <textarea class="form-control" name="historial_medicamentos"></textarea>
            </div>
            <div class="mb-3">
                <label for="alergias" class="form-label">Alergias (opcional)</label>
                <textarea class="form-control" name="alergias"></textarea>
            </div>
            <div class="mb-3">
                <label for="cirugias_previas" class="form-label">Cirugías Previas (opcional)</label>
                <textarea class="form-control" name="cirugias_previas"></textarea>
            </div>
            <div class="mb-3">
                <label for="historial_familiar" class="form-label">Historial Familiar (opcional)</label>
                <textarea class="form-control" name="historial_familiar"></textarea>
            </div>
            <div class="mb-3">
                <label for="nivel_estres" class="form-label">Nivel de Estrés (1-10, opcional)</label>
                <input type="number" min="1" max="10" class="form-control" name="nivel_estres">
            </div>
            <div class="mb-3">
                <label for="ansiedad" class="form-label">Ansiedad (1-10, opcional)</label>
                <input type="number" min="1" max="10" class="form-control" name="ansiedad">
            </div>
            <div class="mb-3">
                <label for="depresion" class="form-label">Depresión (1-10, opcional)</label>
                <input type="number" min="1" max="10" class="form-control" name="depresion">
            </div>
            <div class="mb-3">
                <label for="calidad_sueno_horas" class="form-label">Horas de Sueño Promedio (opcional)</label>
                <input type="number" step="0.1" class="form-control" name="calidad_sueno_horas">
            </div>
            <div class="mb-3">
                <label for="calidad_sueno_nivel" class="form-label">Calidad del Sueño (opcional)</label>
                <input type="text" class="form-control" name="calidad_sueno_nivel">
            </div>
            <div class="mb-3">
                <label for="recuperacion_fisica" class="form-label">Recuperación Física (opcional)</label>
                <input type="text" class="form-control" name="recuperacion_fisica">
            </div>

            <!-- Actividad Física -->
            <h3>Actividad Física</h3>
            <div class="mb-3">
                <label for="fecha_registro_actividad" class="form-label">Fecha de Registro</label>
                <input type="date" class="form-control" name="fecha_registro_actividad" required>
            </div>
            <div class="mb-3">
                <label for="promedio_pasos_diarios" class="form-label">Promedio de Pasos Diarios (opcional)</label>
                <input type="number" class="form-control" name="promedio_pasos_diarios">
            </div>
            <div class="mb-3">
                <label for="minutos_actividad_moderada" class="form-label">Minutos de Actividad Moderada (opcional)</label>
                <input type="number" class="form-control" name="minutos_actividad_moderada">
            </div>
            <div class="mb-3">
                <label for="frecuencia_entrenamiento" class="form-label">Frecuencia de Entrenamiento (días/semana, opcional)</label>
                <input type="number" class="form-control" name="frecuencia_entrenamiento">
            </div>
            <div class="mb-3">
                <label for="deportes_practicados" class="form-label">Deportes Practicados (opcional)</label>
                <textarea class="form-control" name="deportes_practicados"></textarea>
            </div>

            <!-- Exámenes Médicos -->
            <h3>Exámenes Médicos</h3>
            <div class="mb-3">
                <label for="fecha_examen" class="form-label">Fecha de Examen</label>
                <input type="date" class="form-control" name="fecha_examen" required>
            </div>
            <div class="mb-3">
                <label for="electrocardiograma" class="form-label">Electrocardiograma (opcional)</label>
                <input type="text" class="form-control" name="electrocardiograma">
            </div>
            <div class="mb-3">
                <label for="prueba_esfuerzo" class="form-label">Prueba de Esfuerzo (opcional)</label>
                <input type="text" class="form-control" name="prueba_esfuerzo">
            </div>
            <div class="mb-3">
                <label for="perfil_lipidico" class="form-label">Perfil Lipídico (opcional)</label>
                <input type="text" class="form-control" name="perfil_lipidico">
            </div>
            <div class="mb-3">
                <label for="hemoglobina_glicosilada" class="form-label">Hemoglobina Glicosilada (%, opcional)</label>
                <input type="number" step="0.1" class="form-control" name="hemoglobina_glicosilada">
            </div>
            <div class="mb-3">
                <label for="creatinina" class="form-label">Creatinina (mg/dL, opcional)</label>
                <input type="number" step="0.1" class="form-control" name="creatinina">
            </div>
            <div class="mb-3">
                <label for="urea" class="form-label">Urea (mg/dL, opcional)</label>
                <input type="number" step="0.1" class="form-control" name="urea">
            </div>
            <div class="mb-3">
                <label for="tgo" class="form-label">TGO (U/L, opcional)</label>
                <input type="number" step="0.1" class="form-control" name="tgo">
            </div>
            <div class="mb-3">
                <label for="tgp" class="form-label">TGP (U/L, opcional)</label>
                <input type="number" step="0.1" class="form-control" name="tgp">
            </div>
            <div class="mb-3">
                <label for="densitometria_osea" class="form-label">Densitometría Ósea (opcional)</label>
                <input type="text" class="form-control" name="densitometria_osea">
            </div>
            <div class="mb-3">
                <label for="vitamina_d" class="form-label">Vitamina D (ng/mL, opcional)</label>
                <input type="number" step="0.1" class="form-control" name="vitamina_d">
            </div>
            <div class="mb-3">
                <label for="evaluacion_postura" class="form-label">Evaluación de Postura (opcional)</label>
                <input type="text" class="form-control" name="evaluacion_postura">
            </div>

            <button type="submit" class="btn" style="background-color: #003087; color: #FFFFFF; border: 1px solid #A9A9A9;">Agregar Colaborador</button>
        </form>
    </div>
</div>

<?php
echo '</div>'; // Cierra el container principal
echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>';
echo '</body>';
echo '</html>';
?>