<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-02-03
 * Time: 18:08
 */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserConfirmation
 * @package App\Entity
 * @ApiResource(
 *     collectionOperations={
 *          "post"={
 *              "path"="/users/confirm"
 *          }
 *     },
 *     itemOperations={}
 * )
 */
class UserConfirmation
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=30, max=30)
     */
    public $confirmationToken;
}