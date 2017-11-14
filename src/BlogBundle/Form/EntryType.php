<?php

namespace BlogBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, array("required"=>"required",
        "attr"=>array("class"=>"form-title form-control")))
            ->add('content', TextareaType::class, array("required"=>"required",
                "attr"=>array("class"=>"form-content form-control")))
            ->add('status', ChoiceType::class, array("required"=>"required",
                "choices"=>array(
                    "Public"=>"public",
                    "Private"=>"private"
                ),
                "attr"=>array("class"=>"form-status form-control")))
            ->add('image', FileType::class, array("required"=>false,
                "attr"=>array("class"=>"form-image form-control"), "data_class" => null))
            ->add('category', EntityType::class, array("required"=>"required",
                "class"=>"BlogBundle\Entity\Category",
                "attr"=>array("class"=>"form-category form-control")))
//            ->add('user', EntityType::class, array("required"=>"required",
//                "attr"=>array("class"=>"form-user form-control")))
            ->add('tags', TextType::class, array(
                "mapped"=>false,
                "attr"=>array("class"=>"form-category form-control")
            ))
            ->add('Guardar', SubmitType::class, array("attr"=>array("class"=>"btn btn-success")));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BlogBundle\Entity\Entry'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'blogbundle_entry';
    }


}
