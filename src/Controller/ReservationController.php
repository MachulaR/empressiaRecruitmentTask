<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reservation", name="reservation_")
 */
class ReservationController extends AbstractController
{

    public function __construct()
    {
    }

    /**
     * @Route("/", name="index")
     */
    public function reservation()
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation, [
            'action' => $this->generateUrl('reservation_confirm'),
            'method' => 'POST',
        ]);

        $viewData = [
            'reservationForm' => $form->createView(),
        ];
        return $this->render('makeReservation/reservation.html.twig', $viewData);
    }
}
