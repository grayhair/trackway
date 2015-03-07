<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TeamFormType
 *
 * @package AppBundle\Form\Type
 */
class TeamFormType extends AbstractOverrideType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, $this->overrideOptions('name', [
                'required' => true,
                'trim' => true
            ], $options))
            ->add('memberships', 'entity', $this->overrideOptions('memberships', [
                'class' => 'AppBundle\Entity\Membership',
                'expanded'  => true,
                'multiple'  => true
            ], $options));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Team',
            'override' => false
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_team_form_type';
    }
}
