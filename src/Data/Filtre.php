<?php

namespace App\Data;

use App\Entity\Categories;

class Filtre
{
     /**
     * @var Categories[]
     */
    public $categories = [];

    public int $max;

    public int $min;

    public string $nature;
    public bool $livraison = false;

    public string $etat;
}