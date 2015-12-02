<?php

namespace Mall\Utils;

class Pagination
{
    public $page; //当前页码；
    public $firstRow; // 起始行数；
    public $total; //数据总数
    public $listRows; //列表每页显示行数
    public $stringSegment; //页码标示字段
    public $url; //页面地址
    public $lastPage; //最后页，也是总页数
    public $numLinks; //当前页码

    public function __construct($total, $listRows = 30, $numLinks = '', $stringSegment = 'page', $url = '')
    {
        $lastPage = ceil($total/$listRows);
        $this->lastPage = $lastPage;
        $this->page = $numLinks == false ? 1 : min((int)$numLinks,$lastPage);
        $this->total = $total;
        $this->listRows = $listRows;
        $this->stringSegment = $stringSegment;
        $this->url = $url == '' ? $_SERVER['REQUEST_URI'] : $url;
        $this->firstRow = ($this->page-1) * $listRows;
    }

    /**
     *url网址处理函数:URL后加页码查询信息待赋值
     * @return [type] [description]
     */
    protected function getNewUrl()
    {
        $url = $this->url;
        $parseUrl = parse_url($url);
        $urlQuery = $parseUrl['query']; //单独取出URL的查询字串
        if($urlQuery)
        {
            //因为URL中可能包含了页码信息，我们要把它去掉，以便加入新的页码信息
            $urlQuery = ereg_replace('(^|&)'.$this->stringSegment.'=[0-9]+','',$urlQuery);

            //将处理后的URL的查询字串替换原来的URL的查询字串：
            $newUrl = str_replace($parseUrl['query'],$urlQuery,$url);

            //在URL后加page查询信息，但待赋值
            if($urlQuery)
            {
                 $newUrl .= '&'.$this->stringSegment;
            }
            else
            {
                $newUrl .= $this->stringSegment;
            }
         }
         else
         {
            $newUrl = $url.'?'.$this->stringSegment;
         }

        return $newUrl;
     }

    /**
     * [createLink 输出分页导航条代码]
     * @return [type] [description]
     */
    public function createLink()
    {
        if($this->lastPage > 1)
        {
            $url = $this->getNewUrl();
            $pagenav .= '<div class="span6"><div class="dataTables_paginate paging_bootstrap pagination"><ul><li class=""><a href=' .$url. '=1>首页</a></li>';
            $prepg = $this->page-1; //上一页
            $nextpg = $this->page == $this->lastPage ? 0 : $this->page+1; //下一页
            //上一页
            if($prepg)
            {
                $pagenav .= "<li><a class='prev' href='$url=$prepg'>&laquo;</a></li>";
            }
            else
            {
                $pagenav .= '';
            }

            //计算显示的页码
            if($this->page <= 2)
            {
                $startNum = 5;
                $endNum = 1;
            }
            else
            {
                $startNum = $this->page+2;
                $endNum = $this->page-2;
            }

            //计算要显示页码
            if($this->lastPage < $startNum)
            {
                $num = $this->lastPage;
            }
            else
            {
                $num = $startNum;
            }

            //显示页面
            for ($i = $endNum; $i <= $num; $i++)
            {
                if($this->page == $i)
                {
                    $pagenav .= "<li class='active'><a  href='$url=$i'>$i</a></li>";
                }
                else
                {
                    $pagenav .= "<li class=''><a  href='$url=$i'>$i</a></li>";
                }
            }

            //下一页
            if($nextpg)
            {
                $pagenav .= "<li class='next'><a href='$url=$nextpg'>&raquo;</a></li>";
            }
            else
            {
                $pagenav .= '';
            }
            $pagenav .= "<li class=''><a href='$url=$this->lastPage'>末页</a></li></ul></div></div>";

            return $pagenav;
        }
     }

    /**
     * [createLink 输出分页导航条代码,不带首页和末页]
     *
     * @return HTML
     */
    public function createFrontLink()
    {
        if($this->lastPage > 1)
        {
            $url = $this->getNewUrl();
            $pagenav .= '<ul><li class=""></li>';
            $prepg = $this->page-1; //上一页
            $nextpg = $this->page == $this->lastPage ? 0 : $this->page+1; //下一页
            //上一页
            if($prepg)
            {
                $pagenav .= "<li><a class='prev' href='$url=$prepg'>&lt;</a></li>";
            }
            else
            {
                $pagenav .= '';
            }

            //计算显示的页码
            if($this->page <= 2)
            {
                $startNum = 5;
                $endNum = 1;
            }
            else
            {
                $startNum = $this->page+2;
                $endNum = $this->page-2;
            }

            //计算要显示页码
            if($this->lastPage < $startNum)
            {
                $num = $this->lastPage;
            }
            else
            {
                $num = $startNum;
            }

            //显示页面
            for ($i = $endNum; $i <= $num; $i++)
            {
                if($this->page == $i)
                {
                    $pagenav .= "<li class='cur'><a  href='$url=$i'>$i</a></li>";
                }
                else
                {
                    $pagenav .= "<li class=''><a  href='$url=$i'>$i</a></li>";
                }
            }

            //下一页
            if($nextpg)
            {
                $pagenav .= "<li class='next'><a href='$url=$nextpg'>&gt;</a></li>";
            }
            else
            {
                $pagenav .= '';
            }
            $pagenav .= "<li class=''></li></ul>";

            return $pagenav;
        }
    }

    /**
     * [createDotLinks 输出分页导航条代码,带省略符]
     *
     * @return HTML
     */
    public function createDotLinks()
    {
        $page = $this->page;
        $adjacents = 1;
        $url = $this->getNewUrl();
        $prepg = $this->page-1; //上一页
        $nextpg = $this->page == $this->lastPage ? 0 : $this->page+1; //下一页
        $lastpage = $this->lastPage;
        $lpm1 = $lastpage - 1;								//last page minus 1

        $pagination = "";
        if($lastpage > 1)
        {
            $pagination .= "<ul>";
            //previous button
            if($page > 1)
                $pagination.= "<li><a class='prev' href='$url=$prepg'>&lt;</a></li>";
            else
                $pagination.= "<li><a class=\"disabled\">&lt; </a></li>";

            //pages
            if($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
            {
                for ($counter = 1; $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination .= "<li class='cur'><a>$counter</a></li>";
                    else
                        $pagination .= "<li><a href='$url=$counter'>$counter</a></li>";
                }
            }
            elseif($lastpage >= 7 + ($adjacents * 2))	//enough pages to hide some
            {
                //close to beginning; only hide later pages
                if($page < 2 + ($adjacents * 3))
                {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                    {
                        if ($counter == $page)
                            $pagination .= "<li class='cur'><a>$counter</a></li>";
                        else
                            $pagination .= "<li><a href='$url=$counter'>$counter</a></li>";
                    }
                    $pagination .= "<li><span class=\"elipses\">...</span></li>";
                    $pagination .= "<li><a href='$url=$lpm1'>$lpm1</a></li>";
                    $pagination .= "<li><a href='$url=$lastpage'>$lastpage</a></li>";
                }
                //in middle; hide some front and some back
                elseif($lastpage - ($adjacents * 2)-1 > $page && $page > ($adjacents * 2))
                {
                    $pagination .= "<li><a href='$url=1'>1</a></li>";
                    $pagination .= "<li><a href='$url=2'>2</a></li>";
                    $pagination .= "<li><span class=\"elipses\">...</span></li>";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                    {
                        if ($counter == $page)
                            $pagination .= "<li class='cur'><a>$counter</a></li>";
                        else
                            $pagination .= "<li><a href='$url=$counter'>$counter</a></li>";
                    }
                    $pagination .= "<li><span>...</span></li>";
                    $pagination .= "<li><a href='$url=$lpm1'>$lpm1</a></li>";
                    $pagination .= "<li><a href='$url=$lastpage'>$lastpage</a></li>";
                }
                //close to end; only hide early pages
                else
                {
                    $pagination .= "<li><a href='$url=1'>1</a></li>";
                    $pagination .= "<li><a href='$url=2'>2</a></li>";
                    $pagination .= "<li><span class=\"elipses\">...</span></li>";
                    for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $page)
                            $pagination .= "<li class='cur'><a>$counter</a></li>";
                        else
                            $pagination .= "<li><a href='$url=$counter'>$counter</a></li>";
                    }
                }
            }

            //next button
            if ($page < $counter - 1)
                $pagination .= "<li><a href='$url=$nextpg'>&gt;</a></li>";
            else
                $pagination .= "<li><a class=\"disabled\">&gt;</a></li>";
            $pagination .= "</ul>";
        }

        return $pagination;

    }

}
