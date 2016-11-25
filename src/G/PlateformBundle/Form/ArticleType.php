<?php

namespace G\PlateformBundle\Form;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;


use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Arbitrairement, on récupère toutes les catégories qui commencent par "D"
        $pattern = 'D%';
        $builder
            ->add('date', DateType::class, array(
                // render as a single text box
                'widget' => 'single_text',
            ))
            ->add('title',     TextType::class)
            ->add('content',   TextareaType::class)

            ->add('uploadImage', FileType::class, array('label' => 'Image (png file)','required' => false))
            ->add('submit',    SubmitType::class)

        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) {

                $article = $event->getData();

                if (null === $article) {
                    return;
                }

            }
        );
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'G\PlateformBundle\Entity\Articles'
        ));
    }
}