<?php

namespace App\Tests\Form;

use App\Domain\Task\Entity\Task;
use App\Form\TaskFormType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class TaskFormTypeTest extends FormIntegrationTestCase
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
            'title' => 'Nouvelle tâche',
            'description' => 'Description de la tâche',
            'dueDate' => '2025-05-20T14:00:00',
            'status' => Task::STATUS_TODO,
        ];

        $model = new Task();
        $form = $this->factory->create(TaskFormType::class, $model);
        
        $form->submit($formData);
        
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        
        $this->assertEquals('Nouvelle tâche', $model->getTitle());
        $this->assertEquals('Description de la tâche', $model->getDescription());
        $this->assertEquals(new \DateTime('2025-05-20T14:00:00'), $model->getDueDate());
        $this->assertEquals(Task::STATUS_TODO, $model->getStatus());
    }

    public function testSubmitInvalidData()
    {
        $formData = [
            'title' => '',
            'status' => 'INVALID_STATUS',
        ];

        $form = $this->factory->create(TaskFormType::class);
        $form->submit($formData);
        
        $this->assertFalse($form->isValid());
    }
}