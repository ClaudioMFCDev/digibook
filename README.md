# 📚 DigiBook - Plataforma de Venta de Libros Digitales

**DigiBook** es una aplicación web desarrollada como trabajo final para la cátedra de **Ingeniería de Software 2**. El sistema permite la gestión y venta de libros digitales, implementando un flujo completo de comercio electrónico desde el catálogo hasta la simulación de compra.

El proyecto destaca por su rigurosa **trazabilidad**, alineando la implementación técnica con la documentación de requisitos basada en la metodología **NDT (Navigational Development Techniques)**.

## 🚀 Características Principales

* **Catálogo Visual:** Visualización de libros con portadas, precios y detalles (Autor, Género, Editorial).
* **Gestión de Carrito:** Funcionalidad completa de agregar, eliminar ítems y vaciar carrito (usando librería externa).
* **Proceso de Compra:** Registro transaccional de ventas utilizando **Procedimientos Almacenados** en MySQL para garantizar la integridad de datos (Cabecera + Detalles).
* **Administración (ABM):** Formulario para la carga de nuevos libros con soporte para **subida de imágenes** (multipart/form-data).
* **Arquitectura MVC:** Separación limpia de lógica de negocio, datos e interfaz.

## 🛠️ Tecnologías Utilizadas

### Backend & Framework
* **Lenguaje:** PHP 8.1+
* **Framework:** [CodeIgniter 4](https://codeigniter.com/) (MVC)
* **Gestión de Dependencias:** Composer

### Base de Datos
* **Motor:** MySQL 8.0
* **Características:** Uso intensivo de **Stored Procedures** y **Transacciones** para lógica de negocio crítica.

### Frontend
* **Estilos:** Bootstrap 5
* **Librerías:** JQuery (para interacciones AJAX en el carrito).

### Metodología y Herramientas
* **Metodología:** NDT (Navigational Development Techniques).
* **Modelado:** Enterprise Architect (Gantt, Diagramas UML).

## 📋 Requisitos de Instalación

1.  **Servidor Web:** XAMPP, Laragon o similar con Apache y MySQL.
2.  **PHP Extensions:** Deben estar habilitadas `intl`, `mbstring` y `zip` en el `php.ini`.
3.  **Composer:** Instalado globalmente.

## 🔧 Configuración del Proyecto

Sigue estos pasos para levantar el entorno local:

### 1. Clonar y Dependencias
Clona el repositorio en tu carpeta `htdocs` y ejecuta Composer para descargar las librerías:

    git clone https://github.com/ClaudioMFCDev/digibook.git
    cd digibook
    composer install

### 2. Base de Datos
1.  Crea una base de datos en MySQL llamada `digibook2`.
2.  Importa el script SQL ubicado en `/database/digibook2.sql`.
3.  Configura la conexión en el archivo `.env` o en `app/Config/Database.php`.

### 3. Configuración de Imágenes
El sistema almacena las portadas en la carpeta pública. Asegúrate de que exista la siguiente ruta y tenga permisos de escritura:

    public/imagenes/

> **Nota:** Si usas XAMPP, recuerda aumentar el `upload_max_filesize` en tu `php.ini` si planeas subir imágenes de alta resolución.

## 📖 Uso

1.  Accede a `http://localhost/digibook`.
2.  Navega por el catálogo y agrega libros al carrito.
3.  Ingresa al carrito y presiona **"Finalizar Compra"**.
    * *Nota:* No se requiere login para esta demostración de trazabilidad; el sistema asigna la venta a un usuario genérico preconfigurado en la BD.
4.  Para agregar libros, accede a la ruta `http://localhost/digibook/products`.

## ✒️ Autores

* **Castillo, Claudio Marcelo Fabián** - *Desarrollo Backend, Base de Datos y Documentación NDT*
* **Espinoza, Enrique** - *Desarrollo inicial y Procedimientos Almacenados*

---
*Proyecto realizado con fines académicos - 2025*
