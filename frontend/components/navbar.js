$(document).ready(function () {
    // Crear elementos de la barra de navegación
    var navbar = $('<div>').addClass('navbar bg-base-100');
    var navbarStart = $('<div>').addClass('navbar-start');
    var navbarCenter = $('<div>').addClass('navbar-center hidden lg:flex');
    var navbarEnd = $('<div>').addClass('navbar-end');

    // Función para crear un menú desplegable
    function createDropdown() {
        var dropdown = $('<div>').addClass('dropdown');
        var btn = $('<div>').attr({ 'tabindex': '0', 'role': 'button' }).addClass('btn btn-ghost lg:hidden');
        var svg = $('<svg>').attr({
            'xmlns': 'http://www.w3.org/2000/svg',
            'class': 'h-5 w-5',
            'fill': 'none',
            'viewBox': '0 0 24 24',
            'stroke': 'currentColor'
        }).append($('<path>').attr({
            'stroke-linecap': 'round',
            'stroke-linejoin': 'round',
            'stroke-width': '2',
            'd': 'M4 6h16M4 12h8m-8 6h16'
        }));

        var menu = $('<ul>').attr({ 'tabindex': '0' }).addClass('menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52')
            .append($('<li>').append($('<a>').text('Item 1')))
            .append($('<li>').append(
                $('<a>').text('Parent'),
                $('<ul>').addClass('p-2')
                    .append($('<li>').append($('<a>').text('Submenu 1')))
                    .append($('<li>').append($('<a>').text('Submenu 2')))
            ))
            .append($('<li>').append($('<a>').text('Item 3')));

        dropdown.append(btn.append(svg), menu);
        return dropdown;
    }

    // Función para crear un elemento de navegación
    function createNavItem(text, href) {
        var currentPage = window.location.pathname.split('/').pop();
        var isActive = (currentPage === href.split('/').pop());

        return $('<li>').append(
            $('<a>').attr('href', href).toggleClass('active', isActive).text(text)
        );
    }

    // Función para crear un menú desplegable con indicador
    function createDropdownWithIndicator() {
        // ... (tu código existente para este menú)
    }

    // Función para crear un menú desplegable de avatar
    function createAvatarDropdown() {
        // ... (tu código existente para este menú)
    }

    // Agregar elementos al inicio de la barra de navegación
    navbarStart.append(createDropdown());

    // Determinar la página actual
    var currentPage = window.location.pathname.split('/').pop();
    var indexPage = (currentPage === 'index.html') ? 'index.html' : '../index.html';
    navbarStart.append($('<a>').attr('href', indexPage).addClass('btn btn-ghost text-xl').text('Jewernico'));

    // Agregar elementos al centro de la barra de navegación
    var navCenterMenu = $('<ul>').addClass('menu menu-horizontal px-1')
        .append(createNavItem('Productos', 'products.html'))
        .append(createNavItem('Contacto', 'contact.html'))
        .append(createNavItem('About Us', 'about.html'))
        .append(createNavItem('FAQ', 'help.html'));

    navbarCenter.append(navCenterMenu);

    // Agregar elementos al final de la barra de navegación según si el usuario ha iniciado sesión
    if (localStorage.getItem('userId') !== null) {
        navbarEnd.append(createDropdownWithIndicator());
        navbarEnd.append(createAvatarDropdown());
    } else {
        navbarEnd.append($('<a>').addClass('btn').text('Iniciar sesión'));
    }

    // Agregar todos los elementos de la barra de navegación al cuerpo del documento
    navbar.append(navbarStart, navbarCenter, navbarEnd);
    $('body').prepend(navbar);
});
