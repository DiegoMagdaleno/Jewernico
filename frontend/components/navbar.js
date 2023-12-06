$(document).ready(function () {
    var navbar = $('<div>').addClass('navbar bg-base-100');
    var navbarStart = $('<div>').addClass('navbar-start');
    var navbarCenter = $('<div>').addClass('navbar-center hidden lg:flex');
    var navbarEnd = $('<div>').addClass('navbar-end');

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

    function createNavItem(text) {
        return $('<li>').append($('<a>').text(text));
    }

    function createDropdownWithIndicator() {
        var dropdown = $('<div>').addClass('dropdown dropdown-end');
        var btn = $('<div>').attr({ 'tabindex': '0', 'role': 'button' }).addClass('btn btn-ghost btn-circle');
        var indicator = $('<div>').addClass('indicator');
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
            'd': 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'
        }));

        var badge = $('<span>').addClass('badge badge-sm indicator-item').text('0');

        var content = $('<div>').attr({ 'tabindex': '0' }).addClass('mt-3 z-[1] card card-compact dropdown-content w-52 bg-base-100 shadow');
        var cardBody = $('<div>').addClass('card-body');
        var itemText = $('<span>').addClass('font-bold text-lg').text('0 Items');
        var subtotal = $('<span>').addClass('text-info').text('Subtotal: $0');
        var cardActions = $('<div>').addClass('card-actions');
        var viewCartBtn = $('<button>').addClass('btn btn-primary btn-block').text('Ver carrito');

        cardActions.append(viewCartBtn);
        cardBody.append(itemText, subtotal, cardActions);
        content.append(cardBody);

        indicator.append(svg, badge);
        btn.append(indicator);
        dropdown.append(btn, content);

        return dropdown;
    }

    function createAvatarDropdown() {
        var dropdown = $('<div>').addClass('dropdown dropdown-end');
        var btn = $('<div>').attr({ 'tabindex': '0', 'role': 'button' }).addClass('btn btn-ghost btn-circle avatar');
        var avatarDiv = $('<div>').addClass('w-10 rounded-full');
        var avatarImg = $('<img>').attr({
            'alt': 'Tailwind CSS Navbar component',
            'src': 'https://daisyui.com/images/stock/photo-1534528741775-53994a69daeb.jpg'
        });

        var menu = $('<ul>').attr({ 'tabindex': '0' }).addClass('menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52')
            .append($('<li>').append(
                $('<a>').addClass('justify-between').text('Perfil').append($('<span>'))
            ))
            .append($('<li>').append($('<a>').text('Ajustes')))
            .append($('<li>').append($('<a>').text('Cerrar sesión')));

        avatarDiv.append(avatarImg);
        btn.append(avatarDiv);
        dropdown.append(btn, menu);

        return dropdown;
    }


    navbarStart.append(createDropdown());

    var currentPage = window.location.pathname.split('/').pop();
    if (currentPage === 'index.html') {
        navbarStart.append($('<a>').attr('href', 'index.html').addClass('btn btn-ghost text-xl').text('Jewernico'));
    } else {
        navbarStart.append($('<a>').attr('href', '../index.html').addClass('btn btn-ghost text-xl').text('Jewernico'));
    }

    var navCenterMenu = $('<ul>').addClass('menu menu-horizontal px-1')
        .append(createNavItem('Productos', 'products.html'))
        .append(createNavItem('Contacto', 'contact.html'))
        .append(createNavItem('About Us', 'about.html'))
        .append(createNavItem('FAQ', 'help.html'));

    navbarCenter.append(navCenterMenu);

    if (localStorage.getItem('userId') !== null) {
        navbarEnd.append(createDropdownWithIndicator());
        navbarEnd.append(createAvatarDropdown());
    } else {
        navbarEnd.append($('<a>').addClass('btn').text('Iniciar sesión'));
    }

    navbar.append(navbarStart, navbarCenter, navbarEnd);

    $('body').prepend(navbar);
});