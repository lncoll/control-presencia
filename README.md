# Control de Presencia de Empleados (web para Fichar)

Esta aplicación permite gestionar y controlar las entradas y salidas de los empleados.
De momento está en desarrollo, y seguramente tiene bugs. Voy mejorandolo y depurando poco a poco.
Cualquier sugerencia será bienvenida.

## Características

- Registro de entrada y salida de empleados.
- Geolocalización.
- Generación de informes de asistencia.
- Solicitud de corrección de registros.
- Listados por usuario, tanto en html como en pdf.
- Aplicación sencilla para empresas pequeñas/medianas.

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
4. Acceder con el usuario admin con password 123456789. Cambiar password en la sección "Mis datos".
5. Crear usuarios.

## Uso

1. Acceder a la url configurada

## ToDo

1. Implementar un sencillo servicio de mensajería.
2. Comentar bien el código, (esto tendría que ser el punto 1 ;p )

## Contribuir

1. Hacer un fork del repositorio.
2. Crear un Pull Request.

## Paquetes ajenos.

Se utiliza un par de paquetes ajenos, incluidos en el git.
Estos son;
- fpdf, para crear los informes en formato pdf. Puedes encontrarlo en https://http://www.fpdf.org/.
- Leaflet, librería en JavaScript para mostrar los mapas. Su creador es de Ucrania y pide que si su librería es util pienses donar a alguna organización que apoye a la gente ucrana. Visita su web en https://leafletjs.com/.

## Licencia

Este proyecto está licenciado bajo la Licencia GPL3. Consulta el archivo gpl-3.0.txt para más detalles.