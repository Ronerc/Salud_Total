<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Paciente</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    
    <link rel="stylesheet" href="inicio.css" /> 
</head>
<body class="bg-light"> 
    <nav id="navbar-container"><?php include("navbar.php")?></nav>

    <div class="container mt-5 mb-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-dark text-white p-4">
                <h3 class="fw-bold mb-0">
                    <i class="bi bi-person-plus-fill me-2"></i> Registro de Nuevo Paciente
                </h3>
                <p class="mb-0 opacity-75">Complete todos los campos obligatorios (*)</p>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form action="pacientes.php" method="POST"> 
                    
                    <div class="row g-4">
                        
                        <div class="col-md-6">
                            <label for="nombre" class="form-label fw-bold">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellido" class="form-label fw-bold">Apellido *</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                        </div>

                        <div class="col-md-6">
                            <label for="dni" class="form-label fw-bold">DNI *</label>
                            <input type="text" class="form-control" id="dni" name="dni" required>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_nacimiento" class="form-label fw-bold">Fecha de Nacimiento *</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_de_nacimiento" required>
                        </div>

                        <div class="col-md-6">
                            <label for="telefono" class="form-label fw-bold">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono">
                        </div>
                        <div class="col-md-6">
                            <label for="correo_electronico" class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo_electronico" name="correo_electronico">
                        </div>

                        <div class="col-12">
                            <label for="direccion" class="form-label fw-bold">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion">
                        </div>

                        <div class="col-12">
                            <label for="obra_social" class="form-label fw-bold">Obra Social</label>
                            <select id="obra_social" name="obra_social" class="form-select">
                                <option selected>Seleccione...</option>
                                <option value="OSDE">OSDE</option>
                                <option value="Swiss Medical">Swiss Medical</option>
                                <option value="PAMI">PAMI</option>
                                <option value="Particular">Particular</option>
                            </select>
                        </div>
                        
                        <div class="col-12"><hr class="mt-4 mb-2"></div>

                        <div class="col-12 text-end">
                            <a href="pacientes.php" class="btn btn-secondary me-2">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i> Guardar Cambios
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