$(document).ready(function () {
    var footerContainer = $('<div>').attr('id', 'custom-footer-container').addClass('bg-gray-100');

    var footerContent = $('<div>').attr('id', 'custom-footer').addClass('isolate p-6 rounded text-center mb-10 text-black');

    footerContent.append(
        $('<h2>').addClass('text-3xl font-bold mb-6').text('¡Consigue un 20% de descuento en tu primer compra!'),
        $('<p>').addClass('mb-8').text('Únete a nosotros mediante tu email para recibir noticias y descuentos exclusivos'),
        $('<form>').attr({ 'method': 'post', 'action': '/contact#contact_form', 'id': 'contact_form', 'accept-charset': 'UTF-8' })
            .append(
                $('<input>').attr({ 'type': 'hidden', 'name': 'form_type', 'value': 'customer' }),
                $('<input>').attr({ 'type': 'hidden', 'name': 'utf8', 'value': '✓' }),
                $('<input>').attr({ 'type': 'hidden', 'name': 'contact[tags]', 'value': 'newsletter' }),
                $('<div>').addClass('flex justify-center items-center')
                    .append(
                        $('<div>').addClass('mb-4 relative flex items-center')
                            .append(
                                $('<input>').attr({
                                    'id': 'NewsletterForm--template--15127409393872__16401218002cee6770',
                                    'type': 'email',
                                    'name': 'contact[email]',
                                    'class': 'field__input p-2 border rounded border-black text-black',
                                    'value': '',
                                    'aria-required': 'true',
                                    'autocorrect': 'off',
                                    'autocapitalize': 'off',
                                    'autocomplete': 'email',
                                    'placeholder': 'Email',
                                    'required': ''
                                }),
                                $('<label>').addClass('field__label sr-only').attr('for', 'NewsletterForm--template--15127409393872__16401218002cee6770').text('Email'),
                                $('<button>').attr('type', 'submit').addClass('ml-2 p-2 rounded bg-transparent')
                                    .append($('<i>').addClass('fas fa-arrow-right text-black'))
                            )
                    )
            )
    );

    var additionalInfo = $('<div>').addClass('container mx-auto py-12 px-4')
        .append(
            $('<div>').addClass('grid grid-cols-1 md:grid-cols-4 gap-4')
                .append(
                    $('<div>').addClass('md:col-span-1')
                        .append(
                            $('<h2>').addClass('text-lg font-bold mb-2 ml-12').text('Menu'),
                            $('<ul>').addClass('space-y-2')
                                .append(
                                    $('<li>').append($('<a>').attr('href', '..index.html').addClass('text-black link link-hover ml-12').text('Inicio')),
                                    $('<li>').append($('<a>').attr('href', 'products.html').addClass('text-black link link-hover ml-12').text('Productos')),
                                    $('<li>').append($('<a>').attr('href', 'contact.html').addClass('text-black link link-hover ml-12').text('Contacto')),
                                    $('<li>').append($('<a>').attr('href', 'about.html').addClass('text-black link link-hover ml-12').text('About Us')),
                                    $('<li>').append($('<a>').attr('href', 'help.html').addClass('text-black link link-hover ml-12').text('FAQ'))
                                )
                        ),
                    $('<div>').addClass('md:col-span-1')
                        .append(
                            $('<h2>').addClass('text-lg font-bold mb-2').text('Nuestras Sucursales'),
                            $('<ul>').addClass('space-y-2')
                                .append(
                                    $('<li>').append($('<p>').addClass('text-black').text('213 Jose Maria Morelos y Pavón, Zona Centro')),
                                    $('<li>').append($('<p>').addClass('text-black').text('513 Blvd. Luis Donaldo Colosio')),
                                    $('<li>').append($('<p>').addClass('text-black').text('Lunes a Viernes de 9 a.m. a 8 p.m.')),
                                    $('<li>').append($('<p>').addClass('text-black').text('Sábados de 10 a.m. a 5 p.m.'))
                                )
                        ),
                    $('<div>').addClass('md:col-span-1')
                        .append(
                            $('<img>').addClass('image w-1/2 h-1/2').attr('src', 'https://i.imgur.com/nGD2wpc.png').attr('alt', 'Elegante joyería de alta calidad.').attr('loading', 'lazy')
                        ),
                    $('<div>').addClass('md:col-span-1')
                        .append(
                            $('<h2>').addClass('text-lg font-bold mb-2 -ml-11').text('Nuestro Compromiso'),
                            $('<ul>').addClass('space-y-2')
                                .append(
                                    $('<li>').append($('<p>').addClass('text-black -ml-11 text-justify').text('En JewerNico no solo nos comprometemos a crear joyas de calidad, sino también, a crear joyas que sean seguras en todos los aspectos, para que así, nuestros clientes tengan la mejor calidad cargando en su cuello!'))
                                )
                        )
                ),
            $('<div>').addClass('flex justify-center items-center mt-16')
                .append(
                    $('<ul>').addClass('flex space-x-4')
                        .append(
                            $('<li>').append($('<a>').attr('href', '#').addClass('text-black text-lg').html('<i class="fab fa-facebook-f"></i>')),
                            $('<li>').append($('<a>').attr('href', '#').addClass('text-black text-lg').html('<i class="fab fa-twitter"></i>')),
                            $('<li>').append($('<a>').attr('href', '#').addClass('text-black text-lg').html('<i class="fab fa-instagram"></i>')),
                            $('<li>').append($('<a>').attr('href', '#').addClass('text-black text-lg').html('<i class="fab fa-pinterest"></i>')),
                            $('<li>').append($('<a>').attr('href', '#').addClass('text-black text-lg').html('<i class="fab fa-tiktok"></i>'))
                        )
                )
        );

    footerContainer.append(footerContent, additionalInfo);

    $('body').append(footerContainer);
});
