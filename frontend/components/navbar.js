$(document).ready(function() {
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

    navbarStart.append(createDropdown());
    navbarStart.append($('<a>').addClass('btn btn-ghost text-xl').text('daisyUI'));

    var navCenterMenu = $('<ul>').addClass('menu menu-horizontal px-1')
        .append(createNavItem('Item 1'))
        .append($('<li>').append(
            $('<details>').append(
                $('<summary>').text('Parent'),
                $('<ul>').addClass('p-2')
                    .append($('<li>').append($('<a>').text('Submenu 1')))
                    .append($('<li>').append($('<a>').text('Submenu 2')))
            )
        ))
        .append(createNavItem('Item 3'));
    navbarCenter.append(navCenterMenu);

    navbarEnd.append($('<a>').addClass('btn').text('Button'));

    navbar.append(navbarStart, navbarCenter, navbarEnd);

    $('body').prepend(navbar);
});