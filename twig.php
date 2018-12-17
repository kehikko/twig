<?php

function twig_init(array $extra_paths = [])
{
    static $twig = null;
    if ($twig !== null) {
        return $twig;
    }

    $templates_vendor = tool_system_find_files(['views'], [cfg(['path', 'vendor'])], 2, true);
    $templates_routes = tool_system_find_files(['views'], [cfg(['path', 'routes'])], 1, true);
    $templates        = array_merge($templates_vendor, $templates_routes, $extra_paths);
    array_unshift($templates, cfg(['path', 'views']));

    $twig_loader = new Twig_Loader_Filesystem($templates);
    $config      = cfg('twig');
    if (!$config) {
        $config = array('cache' => false);
    }
    if (isset($config['cache']) && $config['cache'] !== false) {
        /* expand twig cache path */
        $config['cache'] = tr($config['cache']);
    }

    $twig = new Twig_Environment($twig_loader, $config);

    /* add custom functions to twig here */
    $twig->addFunction(new Twig_Function('t', function ($key) {return tr('{tr:' . $key . '}');}));
    $twig->addFunction(new Twig_Function('tr', 'tr'));
    $twig->addFunction(new Twig_Function('lang', function () {return cfg(['setup', 'lang']);}));
    $twig->addFunction(new Twig_Function('css', function () {return [];}));
    $twig->addFunction(new Twig_Function('javascript', function () {return [];}));
    $twig->addFunction(new Twig_Function('route', function () {return '';}));
    $twig->addFunction(new Twig_Function('authorize', function () {return false;}));

    return $twig;
}

function twig($template, $params = [])
{
    $twig = twig_init();
    return $twig->render($template, $params);
}
