<div class='container'>


    <section class="mt-5">
        <h2 class="m-5 bg-primary b-5 text-center p-5">Página de inicio</h2>
        <div class="container">
            <a href="<?php echo base_url('buy/showCart')?>">Mirar Carrito</a>
            <div class="row mt-3">

                <?php foreach ($productos as $libro): ?>

                    <div class="col-3 mt-2">
                        <div class="card text-center">
                            <div class="card-header">
                                <?php echo $libro['Título'] ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $libro['Autores'] ?></h5>
                                <p class="card-text"><?php echo $libro['Género'] ?></p>
                                <p class="card-text py-3 fs-3 rounded bg-primary">
                                    <?php echo '$' . $libro['Precio'] ?>
                                </p>

                            </div>
                            <div class="card-footer text-body-secondary">
                                <form action="<?= base_url('cart/add/' . $libro['id']) ?>" method="post">
                                    <button type="submit">Agregar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>


    </section>
</div>