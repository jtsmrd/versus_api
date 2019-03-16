<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-03-09
 * Time: 09:12
 */

namespace App\Controller;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\ProfileImage;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class UploadProfileImageAction
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function __invoke(Request $request)
    {
        $profileImage = new ProfileImage();
        $form = $this->formFactory->create(ImageType::class, $profileImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($profileImage);
            $this->entityManager->flush();

            $profileImage->setFile(null);

            return $profileImage;
        }

        throw new ValidationException(
            $this->validator->validate($profileImage)
        );
    }
}