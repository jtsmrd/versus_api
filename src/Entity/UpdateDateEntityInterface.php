<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-02-14
 * Time: 17:56
 */

namespace App\Entity;

interface UpdateDateEntityInterface
{
    public function setUpdateDate(\DateTimeInterface $dateTime): CreateDateEntityInterface;
}