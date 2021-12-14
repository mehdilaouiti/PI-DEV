<?php


namespace App\Data;


use App\Entity\Categorie;
use App\Entity\Fabricant;

class SearchData
{
    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var Categorie[]
     */
    public $categories = [];

    /**
     * @var Fabricant[]
     */
    public $fabricants = [];

    /**
     * @var null|integer
     */
    public $max;

    /**
     * @var null|integer
     */
    public $min;


}