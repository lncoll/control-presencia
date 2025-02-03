# Control de Presencia de Empleados (web para Fichar)

Esta aplicación permite gestionar y controlar las entradas y salidas de los empleados.

## Características

- Registro de entrada y salida de empleados.
- Generación de informes de asistencia.
- Solicitud de corrección de registros.
- Listados por usuario, tanto en html como en pdf.
- Solo PHP sin Javascript, ligero y rápido.
- Aplicación sencilla para empresas pequeñas y acceso desde la red local.

## Requisitos

- PHP 8.0 o superior
- Servidor web Apache o Nginx
- Base de datos MariaDB/MySQL

## Instalación

1. Clonar el repositorio:
    ```bash
    git clone https://github.com/lncoll/control-presencia.git
    ```
2. Configurar Apache/Nginx para acceso a la carpeta del proyecto, o copiar la carpeta en una ruta accesible por el servidor http.
2. Crear base de datos y usuario con derechos.
3. Al acceder por primera vez a la url configurada, se pedirá la configuración y se crearán las tablas necesarias.

## Uso

1. Acceder a la url configurada

## ToDo

1. Implementar un sencillo servicio de mensajería.
2. Comentar bien el código, (esto tendría que ser el punto 1 ;p )

## Contribuir

1. Hacer un fork del repositorio.
2. Crear un Pull Request.

## Licencia

Este proyecto está licenciado bajo la Licencia GPL3. Consulta el archivo gpl-3.0.txt para más detalles.