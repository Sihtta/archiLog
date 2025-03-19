<?php

namespace App\Tests\Form;

use App\Domain\User\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class UserTypeTest extends FormIntegrationTestCase
{
    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();
        return [
            new ValidatorExtension($validator),
        ];
    }

    public function testSubmitValidData()
    {
        $formData = [
            'fullName' => 'Jean Dupont',
            'pseudo' => 'jdupont',
            'plainPassword' => 'password123'
        ];

        $model = new User();
        $form = $this->factory->create(UserType::class, $model);
        
        $form->submit($formData);
        
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        
        $this->assertEquals('Jean Dupont', $model->getFullName());
        $this->assertEquals('jdupont', $model->getPseudo());
        $this->assertEquals('password123', $model->getPlainPassword());
    }

    public function testSubmitInvalidData()
    {
        $formData = [
            'fullName' => '',
            'pseudo' => 'A',
            'plainPassword' => '' 
        ];

        $form = $this->factory->create(UserType::class);
        $form->submit($formData);
        
        $this->assertFalse($form->isValid());
    }
}