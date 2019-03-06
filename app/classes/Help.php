<?php

namespace app\classes;

use app\App;
/**
 * Class Help
 * @package app\lib\help
 */
class Help
{
    /**
     * partone-parttwo-partthree or partone_parttwo_partthree to PartoneParttwoPartthree if reverse false
     * or back if $reverse true
     * @param string $command
     * @return string
     */
    public static function commandToAction($command, $reverse = false)
    {
        if ($reverse) {
            $commandArray = array_diff(preg_split('/(?=[A-Z])/', $command), ['']);
            foreach ($commandArray as &$item) {
                $item = strtolower($item);
            }
            return implode('-', $commandArray);
        }
        $commandArray = preg_split('/[-_]/', $command);
        foreach ($commandArray as &$item) {
            $item = ucfirst($item);
        }
        return implode('', $commandArray);
    }

    /**
     * delete all files and subdirectories in $directory
     * @param $directory
     */
    public static function clearDirectory($directory)
    {
        if (!file_exists($directory) || !is_dir($directory)) {
            return 0;
        }
        $files = array_diff(scandir($directory), ['.', '..']);
        $count = count($files);
        foreach ($files as $file) {
            if (!is_dir($directory . "/" . $file)) {
                unlink($directory . "/" . $file);
            } else {
                $count += self::clearDirectory($directory . '/' . $file);
            }
        }
        rmdir($directory);
        return $count;
    }

    /**
     * @param $name
     * @param $title
     * @return string
     */
    public static function sortBy($name, $title)
    {
        $request = App::getComponent('request');
        $path = $request->get('path');
        $sort = $request->get('get.sort');
        $order = $request->get('get.order');
        $params = $request->get('get');

        $newOrder = $name != $sort ? 'asc' :  ($order == 'asc' ? 'desc' : 'asc');
        $params['sort'] = $name;
        $params['order'] = $newOrder;
        $paramsQuery = http_build_query($params);
        $href = $path . ($paramsQuery ? '?' . $paramsQuery : '');

        $icon = $name == $sort ? '<i class="fa fa-sort-'.($order == 'desc' ? 'desc' : 'asc' ).
            '" aria-hidden="true"></i>  ' : '' ;

        return '<a class="ajax-button" href="'.$href.'">'.$icon.'&nbsp;'.$title.'</a>';
    }

    /**
     * @param $array
     * @param $from
     * @param $to
     * @return array
     */
    public static function arrayMap($array, $from, $to)
    {
        $out = [];
        foreach ($array as $item) {
            $out[$item[$from]] = $item[$to];
        }
        return $out;
    }

    /**
     * @param $glue
     * @param $array
     * @return string
     */
    public static function multiImplode($glue, $array)
    {
        $out = '';
        if (is_array($array)) {
            foreach ($array as $key => $item) {
                $out .= ($glue. $key . $glue . self::multiImplode($glue, $item));
            }
        } else {
            $out .= (str_replace(' ', $glue, $array));

        }
        return $out;
    }

}