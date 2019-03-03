<?php

namespace app\lib;

use app\App;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

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
        $this->limit = isset($params['limit']) ? $params['limit'] : App::getConfig('grids.limit');

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
        $path = App::getRequest('path');
        $params = App::getRequest('get');
        $buttonCount = App::getConfig('grids.paginage_buttons');
        $start = $this->page - round(floor($buttonCount/2));
        $buttonStart = $start < 1 ? 1 : $start;
        $buttonStart = $this->pageCount > $buttonCount && $buttonStart + $buttonCount - 1 > $this->pageCount ?
            $this->pageCount - $buttonCount + 1 : $buttonStart;

        //make pagination html
        $out = '<ul class="pagination">';

        $params['page'] = null;
        $paramsQuery = http_build_query($params);
        $href = $path . ($paramsQuery ? '?' . $paramsQuery : '');
        $out .= '<li '.($this->page == 1 ? 'class="active"' : '').'><a class="ajax-button" href="'.$href.'">&laquo;</a></li>';
        $params['page'] = $this->page == 2 ? null : $this->page - 1;
        $paramsQuery = http_build_query($params);
        $href = $path . ($paramsQuery ? '?' . $paramsQuery : '');
        $out .= '<li '.($this->page == 1 ? 'class="active"' : '').'><a class="ajax-button" href="'.$href.'">&lsaquo;</a></li>';

        for ($i = $buttonStart; $i <= $this->pageCount; $i++) {
            $active = $i == $this->page;
            $params['page'] = $active || $i==1 ? null : $i;
            $paramsQuery = http_build_query($params);
            $href = $path . ($paramsQuery ? '?' . $paramsQuery : '');
            $out .= $active ? '<li class="active"><span>'.$i.'</span></li>' :
                '<li><a class="ajax-button" href="'.$href.'">'.$i.'</a></li>';
            if ($i == $buttonStart + $buttonCount - 1) {
                break;
            }
        }

        $params['page'] = $this->page + 1;
        $paramsQuery = http_build_query($params);
        $href = $path . ($paramsQuery ? '?' . $paramsQuery : '');
        $out .= '<li '.($this->page == $this->pageCount ? 'class="active"' : '').'><a class="ajax-button" href="'.$href.'">&rsaquo;</a></li>';
        $params['page'] = $this->pageCount;
        $paramsQuery = http_build_query($params);
        $href = $path . ($paramsQuery ? '?' . $paramsQuery : '');
        $out .= '<li '.($this->page == $this->pageCount ? 'class="active"' : '').'><a class="ajax-button" href="'.$href.'">&raquo;</a></li>';

        $out .= '</ul>';
        return $out;
    }

    public function info()
    {
        $from = $this->offset + 1;
        return 'Items ' . $from . ' - ' . $this->recordsTo . ' total '. $this->count;
    }
}
