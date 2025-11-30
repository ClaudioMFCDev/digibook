
<!--Muestra una ventana emergente si el libro fue insertado correctamente-->
<?php if (session()->getFlashdata('correct_insert')): ?>
    <script>
        window.onload = function () {
            alert("<?= session()->getFlashdata('correct_insert') ?>");
        };
    </script>
<?php endif; ?>

<!--Muestra una ventana emergente si hubo errores al insertar el libro-->
<?php if (session()->getFlashdata('error_insert')): ?>
    <script>
        window.onload = function () {
            alert("<?= session()->getFlashdata('error_insert') ?>");
        };
    </script>
<?php endif; ?>

<div class="container w-75">
  <h1 class="bg-dark text-light text-center mt-5 rounded p-3">Registrar Nuevo Libro</h1>
  <form action="<?= base_url('create_book') ?>" method="post" enctype="multipart/form-data">    <!-- Nombre -->
    <div class="mb-3">
      <label for="nombre_libro" class="form-label">Título</label>
      <!--old permite mantener el viejo valor que tenía el input cuando se redirige la vista-->
      <!--session(errors) permite saber si existen errores para las validaciones.
      Con session(errors.valor_name) veo qué input según su atributo name tuvo el error-->
      <input type="text" name="nombre_libro" id="nombre_libro" value="<?= old('nombre_libro') ?>"
        class="form-control <?= session('errors.nombre_libro') ? 'is-invalid' : '' ?>">
      <?php if (session('errors.nombre_libro')): ?>
        <div class="invalid-feedback"><?= session('errors.nombre_libro') ?></div>
      <?php endif; ?>
    </div>

     <!-- Precio -->
    <div class="mb-3">
      <label for="precio_libro" class="form-label">Precio</label>
      <input type="number" step="0.01" name="precio_libro" id="precio_libro"
        value="<?= old('precio_libro') ?>"
        class="form-control <?= session('errors.precio_libro') ? 'is-invalid' : '' ?>">
      <?php if (session('errors.precio_libro')): ?>
        <div class="invalid-feedback"><?= session('errors.precio_libro') ?></div>
      <?php endif; ?>
    </div>
    
     <!-- Páginas -->
    <div class="mb-3">
      <label for="paginas_libro" class="form-label">Páginas Totales</label>
      <input type="number" step="1" name="paginas_libro" id="paginas_libro"
        value="<?= old('paginas_libro') ?>"
        class="form-control <?= session('errors.paginas_libro') ? 'is-invalid' : '' ?>">
      <?php if (session('errors.paginas_libro')): ?>
        <div class="invalid-feedback"><?= session('errors.paginas_libro') ?></div>
      <?php endif; ?>
    </div>

      <!-- Fecha Publicación -->
      <div class="mb-3">
      <label for="fecha_libro" class="form-label">Fecha Publicación</label>
      <input type="date" step="1" name="fecha_libro" id="fecha_libro"
        value="<?= old('fecha_libro') ?>"
        class="form-control <?= session('errors.fecha_libro') ? 'is-invalid' : '' ?>">
      <?php if (session('errors.fecha_libro')): ?>
        <div class="invalid-feedback"><?= session('errors.fecha_libro') ?></div>
      <?php endif; ?>
    </div>

     <!-- Genero -->
    <div class="mb-3">
      <label for="genero_libro" class="form-label">Genero</label>
      <select name="genero_libro" name="genero_libro">

        <?php foreach ($generos as $g): ?>
          <option value="<?= $g->idGenero ?>" <?= old('genero_libro') == $g->idGenero ? 'selected' : '' ?>>
            <?= esc($g->nombre) ?>
          </option>

        <?php endforeach; ?>
      </select>
      <?php if (session('errors.genero_libro')): ?>
        <div class="invalid-feedback"><?= session('errors.genero_libro') ?></div>
      <?php endif; ?>
    </div>

     <!-- Autor -->
    <div class="mb-3">
      <label for="autor_libro" class="form-label">Autor</label>
      <select name="autor_libro" name="autor_libro">

        <?php foreach ($autores as $a): ?>
          <option value="<?= $a->idAutor ?>" <?= old('autor_libro') == $a->idAutor ? 'selected' : '' ?>>
            <?= esc($a->nombre . $a->apellido) ?>
          </option>

        <?php endforeach; ?>
      </select>
      <?php if (session('errors.autor_libro')): ?>
        <div class="invalid-feedback"><?= session('errors.autor_libro') ?></div>
      <?php endif; ?>
    </div>

     <!-- Editorial -->
    <div class="mb-3">
      <label for="editorial_libro" class="form-label">Editorial</label>
      <select name="editorial_libro" name="autor_libro">

        <?php foreach ($editoriales as $e): ?>
          <option value="<?= $e->idEditorial ?>" <?= old('editorial_libro') == $e->idEditorial ? 'selected' : '' ?>>
            <?= esc($e->nombre) ?>
          </option>

        <?php endforeach; ?>
      </select>
      <?php if (session('errors.editorial_libro')): ?>
        <div class="invalid-feedback"><?= session('errors.editorial_libro') ?></div>
      <?php endif; ?>
    </div>

     <!-- Sinopsis -->
     <div class="mb-3">
        <label for="sinopsis_libro" class="form-label">Sinopsis</label>
        <textarea name="sinopsis_libro" id="sinopsis_libro" rows="4" class="form-control
          <?= session('errors.sinopsis_libro') ? 'is-invalid' : '' ?>"><?= old('sinopsis_libro') ?></textarea>
        <?php if(session('errors.sinopsis_libro')): ?>
            <div class="invalid-feedback"><?= session('errors.sinopsis_libro') ?></div>
        <?php endif; ?>
    </div>

    <!-- Imagen/portada -->    
    <div class="mb-3">
        <label for="imagen" class="form-label">Portada del Libro</label>
        <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*">
    </div>

    <button type="submit" class="btn btn-primary">Cargar</button>
  </form>
</div>