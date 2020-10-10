<?php

namespace App\Controller;

use App\Entity\Reservations;
use App\Form\CancelReservationType;
use App\Form\ReservationType;
use App\Repository\ReservationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reservation/cancel", name="reservation_")
 */
class CancelReservationController extends AbstractController
{
    /** @var FlashBagInterface */
    private $flashBag;

    /**
     * @param FlashBagInterface $flashBag
     */
    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    /**
     * @Route("/", name="cancel")
     */
    public function cancelReservation()
    {
        $formData = [
            'reservationNumber' => null,
        ];
        $form = $this->createForm(CancelReservationType::class, $formData, [
            'action' => $this->generateUrl('reservation_cancel_confirm'),
        ]);

        $viewData = [
            'cancelReservationForm' => $form->createView(),
        ];
        return $this->render('cancelReservation/reservation-cancel.html.twig', $viewData);
    }

    /**
     * @Route("/confirm", name="cancel_confirm")
     * @param Request $request
     * @return Response
     */
    public function isReservationPossibleToCancel(Request $request)
    {
        return $this->render('cancelReservation/reservation-cancel-confirm.html.twig');
    }
}
