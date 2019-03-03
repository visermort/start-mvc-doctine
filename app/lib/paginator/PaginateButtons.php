<?php

namespace app\lib\paginator;

class PaginateButtons
{
    protected $currentPage;
    protected $url;
    protected $getParams;
    protected $linkClasses;
    protected $itemClasses;

    public function __construct($options)
    {
        $this->url = !empty($options['url']) ? $options['url'] : '/';
        $this->currentPage = !empty($options['current_page']) ? $options['current_page'] : 1;
        $this->getParams = !empty($options['get_params']) ? $options['get_params'] : [];
        $this->itemClasses = !empty($options['item_classes']) ? $options['item_classes'] : [];
        $this->linkClasses = !empty($options['link_classes']) ? $options['link_classes'] : [];
    }

    public function getButton($page, $label)
    {
        $getParams = $this->getParams;
        $getParams['page'] = $page < 2 ? null : $page;
        $paramsQuery = http_build_query($getParams);
        $href = $this->url . ($paramsQuery ? '?' . $paramsQuery : '');
        $itemClasses = $this->itemClasses;
        if ($page == $this->currentPage) {
            $itemClasses[] = 'active';
        }
        return '<li ' . (!empty($itemClasses) ? 'class="' . implode(' ', $itemClasses) . '"' : '') . '>' .
            '<a ' . (!empty($this->linkClasses) ? 'class="' . implode(' ', $this->linkClasses) . '"' : '') .
            ' href="' . $href . '">' .
            $label .
            '</a></li>';
    }
}