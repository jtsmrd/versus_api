<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-02-03
 * Time: 13:27
 */

namespace App\Entity;

interface CreateDateEntityInterface
{
    public function setCreateDate(\DateTimeInterface $dateTime): CreateDateEntityInterface;
}