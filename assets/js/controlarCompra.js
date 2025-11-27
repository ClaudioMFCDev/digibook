document.querySelectorAll('.btn-comprar').forEach(boton => {
    boton.addEventListener('click', function () {
        Swal.fire({
            title: 'Confimar',
            text: "¿Deseas registrar la compra de los productos en el carrito?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('buy/registrar', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Compra Realizada', data.message, 'success').then(() => {
                            location.reload(); // Recargar la página o actualizar el carrito dinámicamente
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'No se pudo registrar la compra.', 'error');
                });
            }
        });
    });
});