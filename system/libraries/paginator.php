<?php

class Paginator {

    private $per_page = 10;
    private $total = 0;
    private $current_page;
    private $num_pages;
    private $pages;

    function paginate($current, $total, $per_page = 10){
        $this->current_page = $current;
        $this->total = $total;
        $this->per_page = $per_page;
        $this->num_pages = ceil($this->total/$this->per_page);
        if($this->current_page > $this->num_pages) $this->current_page = $this->num_pages;
        $this->pages .= '<ul class="page-list">';
        for($i = 1; $i <= $this->num_pages; $i++){
            $this->pages .= ($i == $this->current_page) ? '<li class="current">'.$i.'</li>' : '<li><a onClick="setPage('.$i.')" href="#">'.$i.'</a></li>';
        }
        $this->pages .= '</ul>';
    }

    function getPagesList(){
        return $this->pages;
    }
}

?>
