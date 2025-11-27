<nav class="navbar navbar-expand-lg bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand text-light" href="#">Digibook</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ">
        <li class="nav-item">
          <a class="nav-link active text-light" aria-current="page"  href=<?php echo base_url('/');?>>Home</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle bg-dark text-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Productos
          </a>
          <ul class="dropdown-menu">
          <a class="nav-link text-dark" href=<?php echo base_url('products');?>>Nuevo</a>
          </ul>
        </li>
       
        
      </ul>
     
    </div>
  </div>
</nav>