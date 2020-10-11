<?php
namespace App\Form;

use App\Entity\Hotel;
use App\Repository\HotelRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ReservationType extends AbstractType
{
    /** @var HotelRepository */
    private $hotelRepository;

    /**
     * ReservationType constructor.
     * @param HotelRepository $hotelRepository
     */
    public function __construct(HotelRepository $hotelRepository)
    {
        $this->hotelRepository = $hotelRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hotel', EntityType::class, [
                'class' => Hotel::class,
                'choice_label' => 'reservationForm',
                'placeholder' => 'Choose a place to stay...',
                'label' => false,
                'required' => true,
            ])
            ->add('startDate', DateType::class, [
                'input' => 'datetime_immutable',
                'format' => 'y-M-d',
                'label' => "From:",
                'required' => true,
            ])
            ->add('endDate', DateType::class, [
                'input' => 'datetime_immutable',
                'format' => 'y-M-d',
                'label' => "To:",
                'required' => true,
            ])
            ->add('beds', NumberType::class, [
                'attr' => [
                    'placeholder' => 'How many beds you need?',
                ],
                'label' => false,
                'required' => true,
            ])
            ->add('Check', SubmitType::class, [
                'attr' => array('class' => 'submit-button'),
            ])
        ;
    }
}