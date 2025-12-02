<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
  <title>Document</title>
</head>

<body>
  <div class="p-4 rounded-3 text-white bg-dark mt-3">
    <div class="d-flex align-items-fluid">
      <div class="p-3 me-3 rounded-2" style="background-color: rgba(255, 255, 255, 0.15);">
        <i class="bi bi-activity fs-3"></i>
      </div>

      <div>
        <h1 class="fw-bold mb-0 fs-3">Bienvenido <?= $_SESSION["nombre_usuario"] ?? "Invitado"; ?></h1>
        <p class="mb-1 small opacity-75">Sistema de gestión médica profesional</p>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>
</body>

</html>