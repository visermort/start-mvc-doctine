<?php

namespace app\components;

use app\App;
use app\Component;
/**
 * Class Help
 * @package app\components
 */
class Help extends Component
{
    /**
     * @param $name
     * @param $title
     * @return string
     */
    public function sortBy($name, $title)
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
    public function arrayMap($array, $from, $to)
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
    public function multiImplode($glue, $array)
    {
        $out = '';
        if (is_array($array)) {
            foreach ($array as $key => $item) {
                $out .= ($glue. $key . $glue . $this->multiImplode($glue, $item));
            }
        } else {
            $out .= (str_replace(' ', $glue, $array));

        }
        return $out;
    }


}