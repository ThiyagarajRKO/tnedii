<?php

if (!function_exists('log_viewer')) {
    /**
     * Get the LogViewer instance.
     *
     * @return \Impiger\LogViewer\Contracts\LogViewer
     */
    function log_viewer()
    {
        return app('impiger::log-viewer');
    }
}

if (!function_exists('log_levels')) {
    /**
     * Get the LogLevels instance.
     *
     * @return \Impiger\LogViewer\Contracts\Utilities\LogLevels
     */
    function log_levels()
    {
        return app('impiger::log-viewer.levels');
    }
}

if (!function_exists('log_menu')) {
    /**
     * Get the LogMenu instance.
     *
     * @return \Impiger\LogViewer\Contracts\Utilities\LogMenu
     */
    function log_menu()
    {
        return app('impiger::log-viewer.menu');
    }
}

if (!function_exists('log_styler')) {
    /**
     * Get the LogStyler instance.
     *
     * @return \Impiger\LogViewer\Contracts\Utilities\LogStyler
     */
    function log_styler()
    {
        return app('impiger::log-viewer.styler');
    }
}

if (!function_exists('extract_date')) {
    /**
     * Extract date from string (format : YYYY-MM-DD).
     *
     * @param  string $string
     * @return string
     */
    function extract_date($string)
    {
        return preg_replace(
            '/.*(' . REGEX_DATE_PATTERN . ').*/',
            '$1',
            $string
        );
    }
}
