<?php

namespace App\Tests\Form;

use App\Form\UserPasswordType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class UserPasswordTypeTest extends FormIntegrationTestCase
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
            'plainPassword' => [
                'first' => 'password123',
                'second' => 'password123'
            ],
            'newPassword' => 'newpassword456'
        ];

        $form = $this->factory->create(UserPasswordType::class);
        
        $form->submit($formData);
        
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertEquals('password123', $form->get('plainPassword')->getData());
        $this->assertEquals('newpassword456', $form->get('newPassword')->getData());
    }

    public function testSubmitInvalidData()
    {
        $formData = [
            'plainPassword' => [
                'first' => 'password123',
                'second' => 'wrongpassword'
            ],
            'newPassword' => '' 
        ];

        $form = $this->factory->create(UserPasswordType::class);
        $form->submit($formData);
        
        $this->assertFalse($form->isValid());
    }
}