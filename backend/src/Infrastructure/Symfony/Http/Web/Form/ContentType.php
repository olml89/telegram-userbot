<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Http\Web\Form;

use olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Http\Web\Request\Content\UploadContentRequestData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Content Name',
                'attr' => [
                    'placeholder' => 'Enter content name...',
                    'class' => 'form-input',
                ],
                'help' => 'A descriptive name for this content item',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter description...',
                    'class' => 'form-textarea',
                    'rows' => 4,
                ],
                'help' => 'Optional description of the content',
            ])
            ->add('mediaFile', FileType::class, [
                'label' => 'Media File',
                'attr' => [
                    'class' => 'form-file',
                    'accept' => 'image/*,video/*,audio/*,.pdf',
                ],
                'help' => 'Upload image or video (max 2 GB)',
            ])
            ->add('tags', ChoiceType::class, [
                'label' => 'Tags',
                'choices' => [
                    'Welcome' => 'welcome',
                    'Help' => 'help',
                    'Command' => 'command',
                    'Auto Reply' => 'auto-reply',
                    'Marketing' => 'marketing',
                    'Support' => 'support',
                    'FAQ' => 'faq',
                    'Notification' => 'notification',
                ],
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-select-multiple',
                    'size' => 5,
                ],
                'help' => 'Select one or more tags (hold Ctrl/Cmd to select multiple)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UploadContentRequestData::class,
        ]);
    }
}
