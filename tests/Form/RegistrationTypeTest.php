<?php

namespace App\Tests\Form;

use App\Domain\User\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class RegistrationTypeTest extends FormIntegrationTestCase
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
            'fullName' => 'John Doe',
            'pseudo' => 'johnd',
            'email' => 'john@example.com',
            'plainPassword' => [
                'first' => 'password123',
                'second' => 'password123'
            ]
        ];

        $model = new User();
        $form = $this->factory->create(RegistrationType::class, $model);
        
        $form->submit($formData);
        
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        
        $this->assertEquals('John Doe', $model->getFullName());
        $this->assertEquals('johnd', $model->getPseudo());
        $this->assertEquals('john@example.com', $model->getEmail());
        $this->assertEquals('password123', $model->getPlainPassword());
    }

    public function testSubmitInvalidData()
    {
        $formData = [
            'fullName' => '',
            'email' => 'invalid-email',
            'plainPassword' => [
                'first' => 'short',
                'second' => 'mismatch'
            ]
        ];

        $form = $this->factory->create(RegistrationType::class);
        $form->submit($formData);
        
        $this->assertFalse($form->isValid());
    }
}
