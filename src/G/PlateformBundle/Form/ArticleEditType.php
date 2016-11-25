<?php

namespace G\PlateformBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
class ArticleEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('date');
        $builder->remove('uploadImage');
    }
    public function getParent()
    {
        return ArticleType::class;
    }
}