<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Horario de Atención</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light"> 
    <nav id="navbar-container"><?php include("navbar.php")?></nav>
    <div class="container mt-5 mb-5">
        <div class="card shadow-lg border-0">
            
            <div class="card-header bg-dark text-white p-4">
                <h3 class="fw-bold mb-0">
                    <i class="bi bi-clock-fill me-2"></i> Registrar Nuevo Horario
                </h3>
                <p class="mb-0 opacity-75">Defina el horario de atención para un médico y especialidad</p>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form action="administracion.php" method="POST"> <div class="row g-4">
                        
                        <div class="col-md-6">
                           <label for="id_medico" class="form-label">Médico</label>
                            <select name="id_medico" id="id_medico" class="form-control" required>
                                <option selected disabled>Seleccione un médico</option>

                                <?php
                                $res = $conn->query("SELECT id_medico, nombre FROM medicos");
                                while ($m = $res->fetch_assoc()):
                                ?>
                                    <option value="<?= $m['id_medico'] ?>">
                                        <?= htmlspecialchars($m['nombre']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="id_especialidades" class="form-label fw-bold">Especialización *</label>
                            <select id="id_especialidades" name="id_especialidades" class="form-select" required>
                                <option value="" selected disabled>Seleccione la especialidad...</option>
                                <option value="1">Clínico de rodillas</option>
                                <option value="2">lee las palmas de los pies</option>
                                <option value="3">Cardiología</option>
                                </select>
                        </div>

                        <div class="col-md-4">
                            <label for="dia_semana" class="form-label fw-bold">Día de la Semana *</label>
                            <select id="dia_semana" name="dia_semana" class="form-select" required>
                                <option value="" selected disabled>Seleccione el día...</option>
                                <option value="Lunes">Lunes</option>
                                <option value="Martes">Martes</option>
                                <option value="Miércoles">Miércoles</option>
                                <option value="Jueves">Jueves</option>
                                <option value="Viernes">Viernes</option>
                                <option value="Sábado">Sábado</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="hora_inicio" class="form-label fw-bold">Hora Inicio *</label>
                            <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                        </div>

                        <div class="col-md-4">
                            <label for="hora_fin" class="form-label fw-bold">Hora Fin *</label>
                            <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                        </div>

                        <div class="col-12"><hr class="mt-4 mb-2"></div>

                        <div class="col-12 text-end">
                            <a href="administracion.html" class="btn btn-secondary me-2">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i> Guardar Horario
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>