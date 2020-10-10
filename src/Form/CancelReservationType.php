<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CancelReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('reservationNumber', TextType::class, [
                'attr' => [
                    'placeholder' => 'reservation number',
                ],
                'label' => false,
                'required' => true,
            ])
            ->add('Cancel_Reservation', SubmitType::class, [
                'attr' => [
                    'class' => 'submit-button'
                ],
            ])
        ;
    }

}
