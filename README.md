# Jewernico 
### Sitio de compras simulado

## ¿Como correr este proyecto?

Este proyecto utiliza diversas herramientas para poder ser ejecutado desde cero.

**Software requerido**

- [Node.JS](https://nodejs.org/dist/v20.10.0/node-v20.10.0-x64.msi)
- [Composer](https://getcomposer.org/download/)

**Configuracion requerida**

Es necesario configurar Apache y XAMPP para que se pueda acceder al sitio web desde jewernico.com (El dominio no es real, pero configuraremos a Wndows para que asi lo sea).

**Configuracion de Windows**

Como administrador, ejecutar el Bloc de Notas. Una vez ejecutado, abrir el siguiente archivo `C:\Windows\System32\drivers\etc\hosts`.

Con el archivo abierto, copiar la siguiente entrada al final del archivo

```
127.0.0.1   jewernico.com
```

Guardar el archivo.

**Donde clonar el repostiorio**

Para que todo funcione correctamente, debera de clonarse el repositorio en la siguiente ruta (Forzosamente) `C:\xampp\htdocs\Jewernico` (Es decir, ejecutar el clonado del repositorio en htdocs)

*¿Como clonar el repo*

Una vez abierta la terminal, ejecutar los siguientes comandos en orden

```
cd C:\xampp\htdocs
gh repo clone DiegoMagdaleno/Jewernico
cd Jewernico
```

**Configuracion de XAMPP y Apache**

Una vez clonado el repositorio, abrir con el Bloc de Notas en modo administrador la siguiente ruta: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

Y copiar el siguiente contenido al final del archivo:

```
<VirtualHost *:80>
        DocumentRoot C:/xampp/htdocs/Jewernico/public
        ServerName jewernico.com
</VirtualHost>
```

**Configuracion de extensiones de PHP**

Para cargar el Captcha PHP debe de ser capaz de generar imagenes, para eso, es necesario habilitar un plugin, por favor, seguir los pasos que se mencionan a continuacion:

- Abrir XAMPP
- Dar click en config en la seccion de Apache
- Buscar `;extension=gd`
- Quitar el `;`

*Si ya no tiene el `;` pues no lo quiten.

**Una vez realizado todo esto, favor de reiniciar su computadora**

## Pasos finales e importantes

Para poder ejecutar todo correctamente, es necesario abrir una terminal y ejecutar los siguientes comandos, todos los comandos deben de ejecutarse desde donde clonaron el repositorio (`C:\xampp\htdocs\Jewernico`):

1. Actualizar e instalar las dependencias de Node con `npm install`
2. Actualizar e instalar los paquetes de PHP con `composer install`
3. Preparar CSS y JS con `npm run build`

## Pasos a realizar para el equipo de frontend cada vez que se modifique un archivo .twig

Debido a la configuracion de Tailwind, las clases de añaden de manera **dinamica** es necesario que cada vez que modifiquen un archivo asi, ejecuten en el directorio de Jewernico **npm run build** y **LIMPIEN EL CACHE DE SU NAVEGADOR DE FORMA OBLIGATORIA, SE RECOMIENDA TENER UN NAVEGADOR SOLO PARA ESTE PROYECTO**

