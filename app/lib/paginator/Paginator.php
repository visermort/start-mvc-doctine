<?php

namespace app\lib\paginator;

use app\App;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use app\lib\paginator\PaginateButton;

/**
 * Class Paginator
 * @package app\lib
 */
class Paginator
{
    protected $limit;
    protected $page = 1;
    protected $offset = 0;
    protected $pageCount = 1;
    protected $single = false;//one page
    protected $count = 0;
    protected $queryResult;
    protected $queryBuilder;
    protected $recordsTo;
    protected $itemClasses = [];
    protected $linkClasses = [];

    /**
     * @param $query
     * @param array $params
     */
    public function __construct($queryBuilder, $params = [])
    {
        $this->params = $params;
        $this->queryBuilder = $queryBuilder;
        $this->single = isset($params['single']) ? $params['single'] : false;
        $this->page = isset($params['page']) ? $params['page'] : $this->page;
        $this->page = $this->page == 0 ? 1 : $this->page;
        $this->limit = isset($params['limit']) ? $params['limit'] : App::getComponent('config')->get('grids.limit');
        $this->itemClasses = isset($params['item_classes']) ? $params['item_classes'] : [];
        $this->linkClasses = isset($params['link_classes']) ? $params['link_classes'] : [];

        if ($this->single) {
            $this->makeSinglePage();
        } else {
            $this->makeMultiPage();
        }
    }

    protected function makeSinglePage()
    {
        $this->offset = 0;
        $query = $this->queryBuilder->getQuery();

        $paginator = new DoctrinePaginator($query);
        $this->count = $paginator->count();
        $this->queryResult = $paginator;
        $this->pageCount = 1;
        $this->recordsTo = $this->count;

        if ($this->page > $this->pageCount) {
            App::getController()->actionNotfound();
        }
    }

    protected function makeMultiPage()
    {
        $this->offset =  $this->limit * ($this->page - 1);
        $query = $this->queryBuilder->setFirstResult($this->offset)->setMaxResults($this->limit);

        $paginator = new DoctrinePaginator($query);
        $this->count = $paginator->count();
        $this->queryResult = $paginator;
        $this->pageCount = $this->count && $this->limit ? ceil($this->count / $this->limit) : 0;
        $this->recordsTo = $this->offset + $this->limit < $this->count ? $this->offset + $this->limit : $this->count;

        if ($this->page > $this->pageCount) {
            App::getController()->actionNotfound();
        }
    }


    public function data()
    {
        return $this->queryResult;
    }

    public function buttons()
    {
        if ($this->single || $this->pageCount == 1) {
            return '';
        }
        $buttonCount = App::getComponent('config')->get('grids.paginage_buttons');
        $start = $this->page - round(floor($buttonCount/2));
        $buttonStart = $start < 1 ? 1 : $start;
        $buttonStart = $this->pageCount > $buttonCount && $buttonStart + $buttonCount - 1 > $this->pageCount ?
            $this->pageCount - $buttonCount + 1 : $buttonStart;

        $request = App::getComponent('request');

        //make pagination html
        $paginateButtons = new PaginateButtons([
            'url' => $request->get('path'),
            'current_page' => $this->page,
            'link_classes' => $this->linkClasses,
            'item_classes' => $this->itemClasses,
            'get_params' => $request->get('get'),
            ]);

        $out = '<ul class="pagination">';
        $out .= $paginateButtons->getButton(1, '&laquo;');
        $out .= $paginateButtons->getButton($this->page > 2  ? $this->page - 1 : 1, '&lsaquo;');
        for ($i = $buttonStart; $i <= $this->pageCount; $i++) {
            $out .= $paginateButtons->getButton($i, $i);
            if ($i == $buttonStart + $buttonCount - 1) {
                break;
            }
        }
        $out .= $paginateButtons->getButton(
            $this->page + 1 > $this->pageCount ? $this->pageCount : $this->page + 1,
            '&rsaquo;'
        );
        $out .= $paginateButtons->getButton($this->pageCount, '&raquo;');
        $out .= '</ul>';
        return $out;
    }

    public function info()
    {
        $from = $this->offset + 1;
        return 'Items ' . $from . ' - ' . $this->recordsTo . ' total '. $this->count;
    }
}
