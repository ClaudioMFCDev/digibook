
document.querySelectorAll('.btn-eliminar').forEach(boton => {
    boton.addEventListener('click', function () {
        const id = this.dataset.id;
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas eliminar este producto del carrito?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`cart/remove/${id}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Eliminado', data.message, 'success').then(() => {
                            location.reload(); // Recargar la página o actualizar el carrito dinámicamente
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'No se pudo eliminar el producto.', 'error');
                });
            }
        });
    });
});

document.querySelectorAll('.btn-eliminar-carrito').forEach(boton => {
    boton.addEventListener('click', function () {
        const id = this.dataset.id;
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas eliminar todo el carrito?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`cart/delete/`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Eliminado', data.message, 'success').then(() => {
                            location.reload(); // Recargar la página o actualizar el carrito dinámicamente
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'No se pudo eliminar el producto.', 'error');
                });
            }
        });
    });
});

