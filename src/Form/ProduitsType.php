<?php

namespace App\Form;

use App\Entity\Produits;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProduitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('imageFile', VichImageType::class, [
            'label' => 'Image ( JPG or PNG file )',
            'required' => false,
            'allow_delete' => true,
            'delete_label' => 'supprimer image',
            'download_uri' => false,
            'imagine_pattern' => 'lataille_meduim',
        ] )
            ->add('titre')
            ->add('description')
            ->add('prix')
            ->add('quantite')
            ->add('createdAt')
            ->add('updatedAt')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
