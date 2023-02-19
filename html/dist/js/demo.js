(function ($) {
    'use strict'

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1)
    }

    function createSkinBlock(colors, callback, selected, name) {
        var $block = $('<select />', {})
        var allClasses = $(selected).attr("class").split(/\s+/);
        var selectedClass = allClasses.slice(-1).pop();
        var $default = $('<option />', {
            text: 'None',
            selected: true
        })

        $block.append($default).addClass('form-control')
        if (callback) {
            $default.on('click', callback)
        }

        colors.forEach(function (color) {
            var $color = $('<option />', {
                text: capitalizeFirstLetter((typeof color === 'object' ? color.join(' ') : color).replace(/navbar-|accent-|bg-/, '').replace('-', ' ')),
                selected: (selectedClass == name + color ? true : false)
            })

            $block.append($color)
            $color.data('color', color)
            if (callback) {
                $color.on('click', callback)
            }
        })
        return $block
    }

    var $sidebar = $('.control-sidebar')
    var $container = $('<div />', {
        class: 'p-3 control-sidebar-content'
    })

    $sidebar.append($container)
    $container.append(
        '<h5>Settings</h5><hr class="mb-2"/>'
    )

    var $dark_mode_checkbox = $('<input />', {
        type: 'checkbox',
        value: 1,
        checked: $('body').hasClass('dark-mode'),
        class: 'mr-1'
    }).on('click', function () {
        var new_value = '';
        if ($(this).is(':checked')) {
            $('body').addClass('dark-mode')
            new_value = 'dark-mode';
        } else {
            $('body').removeClass('dark-mode')
            new_value = '';
        }
        var attr = "index.php?a=sysUserSetSetting&nr=1&value=" + new_value;
        //logConsole("Changing settings nr: 1 to: " + new_value);
        ajax(attr, false);
    })
    var $dark_mode_container = $('<div />', {class: 'mb-4'}).append($dark_mode_checkbox).append('<span>Dark Mode</span>')
    $container.append($dark_mode_container)

    // Color Arrays
    var navbar_dark_skins = [
        'navbar-primary',
        'navbar-secondary',
        'navbar-lightblue',
        'navbar-purple',
        'navbar-teal',
        'navbar-dark',
    ]

    var navbar_light_skins = [
        'navbar-white',
    ]

    var sidebar_colors = [
        'primary',
        'lightblue',
        'purple',
        'teal',
    ]

    var sidebar_skins = [
        'sidebar-dark-primary',
        'sidebar-dark-lightblue',
        'sidebar-dark-purple',
        'sidebar-dark-teal',
        'sidebar-light-primary',
        'sidebar-light-lightblue',
        'sidebar-light-purple',
        'sidebar-light-teal',
    ]

    // Navbar Variants

    $container.append('<h6>Navbar Variants</h6>')

    var $navbar_variants = $('<div />', {
        class: 'd-flex'
    })
    var navbar_all_colors = navbar_dark_skins.concat(navbar_light_skins)
    var $navbar_variants_colors = createSkinBlock(navbar_all_colors, function () {

        var color = $(this).data('color')
        var $main_header = $('.main-header')
        var new_value = '';
        $main_header.removeClass('navbar-dark').removeClass('navbar-light')
        navbar_all_colors.forEach(function (color) {
            $main_header.removeClass(color)
        })

        if (navbar_dark_skins.indexOf(color) > -1) {
            $main_header.addClass('navbar-dark')
            new_value = 'navbar-dark '+ color;
        } else {
            $main_header.addClass('navbar-light')
            new_value = 'navbar-light '+ color;
        }
        $sidebar_light_variants.val("None")
        $main_header.addClass(color)

        var attr = "index.php?a=sysUserSetSetting&nr=2&value=" + new_value;
        logConsole("Changing settings nr: 2 to: " + new_value);
        ajax(attr, false);
    }, $('.main-header'), '')
    $navbar_variants.append($navbar_variants_colors)
    $container.append($navbar_variants)

    // Sidebar Colors
    $container.append('<h6>Light variants</h6>')
    var $sidebar_variants_dark = $('<div />', {
        class: 'd-flex'
    })
    $container.append($sidebar_variants_dark)
    var $sidebar_dark_variants = createSkinBlock(sidebar_colors, function () {
        var color = $(this).data('color')
        var sidebar_class = 'sidebar-dark-' + color
        var $sidebar = $('.main-sidebar')
        sidebar_skins.forEach(function (skin) {
            $sidebar.removeClass(skin)
            $sidebar_light_variants.removeClass(skin).removeClass('text-light')
        })
        $sidebar.addClass(sidebar_class)

        var attr = "index.php?a=sysUserSetSetting&nr=3&value=" + sidebar_class;
        logConsole("Changing settings nr: 3 to: " + sidebar_class);
        ajax(attr, false);
    }, $('.main-sidebar'), 'sidebar-dark-')


    $container.append($sidebar_dark_variants)
    $container.append('<h6>Dark variants</h6>')
    var $sidebar_variants_light = $('<div />', {
        class: 'd-flex'
    })
    $container.append($sidebar_variants_light)
    var $sidebar_light_variants = createSkinBlock(sidebar_colors, function () {
        var color = $(this).data('color')
        var sidebar_class = 'sidebar-light-' + color
        var $sidebar = $('.main-sidebar')
        sidebar_skins.forEach(function (skin) {
            $sidebar.removeClass(skin)
            $sidebar_dark_variants.removeClass(skin).removeClass('text-light')
        })
        $sidebar_dark_variants.val("None")
        $sidebar.addClass(sidebar_class)
        $('.sidebar').removeClass('os-theme-light').addClass('os-theme-dark')

        var attr = "index.php?a=sysUserSetSetting&nr=3&value=" + sidebar_class;
        logConsole("Changing settings nr: 3 to: " + sidebar_class);
        ajax(attr, false);
    }, $('.main-sidebar'), 'sidebar-light-')
    $container.append($sidebar_light_variants)

})(jQuery)
