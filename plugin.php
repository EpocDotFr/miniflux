<?php

class Plugin
{
    private static $menu = array();


    // Setup a plugin is not installed
    public static function setup($plugin_name, $callback)
    {

    }


    // Load all plugins
    public static function loadPlugins()
    {
        self::loadDefaultMenu();

        if (file_exists(PLUGIN_DIRECTORY)) {

            $dir = new \DirectoryIterator(PLUGIN_DIRECTORY);

            foreach ($dir as $fileinfo) {

                if (! $fileinfo->isDot() && $fileinfo->isDir()) {

                    $file = PLUGIN_DIRECTORY.'/'.$fileinfo->getFilename().'/'.$fileinfo->getFilename().'.php';

                    if (file_exists($file)) {
                        require $file;
                    }
                }
            }
        }
    }


    public static function loadDefaultMenu()
    {
        self::addMenu('unread', 'unread', 10, function($menu, $nb_unread_items) {

            return '<a href="?action=unread">'.t('unread').'<span id="nav-counter">'.($nb_unread_items ? '('.$nb_unread_items.')' : '').'</span></a>';
        });

        self::addMenu('bookmarks', 'bookmarks', 20);
        self::addMenu('history', 'history', 30);
        self::addMenu('config', 'settings', 40);
        self::addMenu('logout', 'logout', 50);
    }


    public static function addMenu($name, $title, $position, $callback = null)
    {
        self::$menu[$position.$name] = array(
            'name' => $name,
            'title' => $title,
            'position' => $position,
            'callback' => $callback
        );
    }


    public static function delMenu($name, $position)
    {
        if (isset(self::$menu[$position.$name])) {
            unset(self::$menu[$position.$name]);
        }
    }


    public static function buildMenu($selected_menu, $nb_unread_items)
    {
        ksort(self::$menu);

        $html = '<ul>';

        foreach (self::$menu as $values) {

            $html .= '<li ';
            $html .= $values['name'] === $selected_menu ? 'class="active"' : '';
            $html .= '>';

            if (is_callable($values['callback'])) {

                $html .= $values['callback']($selected_menu, $nb_unread_items);
            }
            else {

                $html .= '<a href="?action='.$values['name'].'">'.t($values['title']).'</a>';
            }

            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }


    public static function getOption($name)
    {
        if (! isset($_SESSION)) {

            return \PicoTools\singleton('db')->table('plugin_options')->findOneColumn($name);
        }
        else {

            if (! isset($_SESSION['plugin_options'])) {
                $_SESSION['plugin_options'] = get_config();
            }

            if (isset($_SESSION['plugin_options'][$name])) {
                return $_SESSION['plugin_options'][$name];
            }
        }

        return null;
    }
}