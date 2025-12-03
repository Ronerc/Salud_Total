<?php
session_start();
include("conexion.php");
include("navbar.php");

$message = "";


// Función helper: prepara, ejecuta e retorna insert_id
function insertarPreparar($conn, $sql, $types, ...$params) {
  $stmt = $conn->prepare($sql);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $id = $stmt->insert_id;
  $stmt->close();
  return $id;
}




// PROCESAR FORMULARIO

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Datos médico
    $nombre  = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $dni = $_POST["dni"];
    $telefono = $_POST["telefono"];
    $correo = $_POST["correo_electronico"];

    // Datos especialidad
    $especialidad = $_POST["especialidad"];
    $horario = trim($_POST["horario"]);
      if ($horario === "") {
          $horario = "Sin horario"; 
      }

    // 1) Insert médico
    $id_medico = insertarPreparar(
        $conn,
        "INSERT INTO medicos (nombre, apellido, dni, telefono, correo_electronico)
         VALUES (?, ?, ?, ?, ?)",
        "sssss",
        $nombre, $apellido, $dni, $telefono, $correo
    );

    // 2) Insert especialidad
    $id_especialidad = insertarPreparar(
        $conn,
        "INSERT INTO especialidades (especialidad, horario)
         VALUES (?, ?)",
        "ss",
        $especialidad, $horario
    );

    // 3) Insert relación
    insertarPreparar(
        $conn,
        "INSERT INTO medicos_especialidades (id_medico, id_especialidad)
         VALUES (?, ?)",
        "ii",
        $id_medico, $id_especialidad
    );

    // 4) Crear usuario automático
    insertarPreparar(
        $conn,
        "INSERT INTO usuarios (nombre, clave) VALUES (?, ?)",
        "ss",
        $nombre, "123"
    );

    $message = '<div class="alert alert-success">Médico registrado correctamente.</div>';
}
?>



<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Administración</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />
  </head>
  <body style="background-color: #f2f7ff">
    
    <div class="container py-4">
      <div class="d-flex align-items-center mb-3">
        <i class="bi bi-gear-fill me-3 fs-2 text-primary"></i>
        <div>
          <h1 class="fw-bold mb-0">Administración</h1>
          <p class="text-muted small mb-0">
            Gestión de médicos, especialidades y horarios
          </p>
        </div>
      </div>

      <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
        <!-- <li class="nav-item" role="presentation">
         
        </li>
        <li class="nav-item" role="presentation">
        
        </li> -->
        <li class="nav-item" role="presentation">
          <!-- <button
            class="nav-link active"
            id="horarios-tab"
            data-bs-toggle="tab"
            data-bs-target="#horarios"
            type="button"
            role="tab"
            aria-controls="horarios"
            aria-selected="true"
          >
            Horarios
          </button> -->
        </li>
      </ul>

      <div class="tab-content" id="adminTabsContent">
        <div
          class="tab-pane fade show active"
          id="horarios"
          role="tabpanel"
          aria-labelledby="horarios-tab"
        >
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Horarios de Atención</h5>
                 <a href="nuevo_medico.php" class="btn btn-primary">
                    Nuevo Medico
                    </a>
                    <a href="nuevo_usuario.php" class="btn btn-warning">
                    Nuevo Usuario
                 </a>
                <!-- <a href="nuevo_horario.php" class="btn btn-primary">
                  <i class="bi bi-person-plus-fill me-1"></i> Nueva Consulta
                </a> -->
              </div>

              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-dark">
                        <tr>
                            <th scope="col">Médico</th>
                            <th scope="col">Especialización</th>
                            <th scope="col">Horario de trabajo</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        $sql = "SELECT 
                                m.id_medicos,
                                m.nombre AS nombre_medico,
                                m.apellido AS apellido_medico,
                                e.especialidad,
                                e.horario
                            FROM medicos m
                            LEFT JOIN medicos_especialidades me ON m.id_medicos = me.id_medico
                            LEFT JOIN especialidades e ON me.id_especialidad = e.id_especialidad";


                        $lista = $conn->query($sql);

                        if (!$lista) {
                            echo "<tr><td colspan='4' class='text-danger'>Error en consulta: " . $conn->error . "</td></tr>";
                        } else {

                            if ($lista->num_rows == 0) {
                                echo "<tr><td colspan='4' class='text-center text-muted'>No hay horarios registrados</td></tr>";
                            } else {

                                while($row = $lista->fetch_assoc()) { ?>
                                
                                    <tr>
                                        <td><?= $row['nombre_medico'] . ' ' . $row['apellido_medico'] ?></td>
                                        <td><?= $row['especialidad'] ?></td>
                                        <td><?= ($row['horario']) ?></td>


                                        <td>
                                            <a href="<?php echo 'editar_medico.php?id=' . urlencode($row['id_medicos']); ?>" 
                                              class="btn btn-sm btn-outline-success me-2" data-id="<?php echo htmlspecialchars($row['id_medicos']); ?>">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </a>
                                            <a href="<?php echo 'eliminar_medico.php?id=' . urlencode($row['id_medicos']); ?>" 
                                              class="btn btn-sm btn-outline-danger" data-id="<?php echo htmlspecialchars($row['id_medicos']); ?>">
                                                <i class="bi bi-trash-fill"></i> Eliminar
                                            </a>
                                        </td>
                                    </tr>

                                <?php }
                            }
                        }
                        ?>

                    </tbody>


                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
        
    </div>
      
        <!-- <a href="nuevo_medico.php" class="btn btn-primary">
           Nuevo Medico
        </a>
        <a href="nuevo_usuario.php" class="btn btn-warning">
           Nuevo Usuario
        </a> -->
      
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>


  </body>
</html>
