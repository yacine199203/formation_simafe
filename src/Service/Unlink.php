<?php
namespace App\Service;

class Unlink
{
    public function sup ()
    {
        unlink('public/images/iris.png');
    }
}