<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\CancelReservationType;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
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
    /** @var ReservationRepository */
    private $reservationsRepository;

    /** @var FlashBagInterface */
    private $flashBag;

    /**
     * @param ReservationRepository $reservationsRepository
     * @param FlashBagInterface $flashBag
     */
    public function __construct(ReservationRepository $reservationsRepository,
                                 FlashBagInterface $flashBag)
    {
        $this->reservationsRepository = $reservationsRepository;
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
        $formData = [
            'reservationNumber' => null,
        ];
        $form = $this->createForm(CancelReservationType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservationNumber = $form->getData()['reservationNumber'];
            $reservation = $this->reservationsRepository->find($reservationNumber) ;
            $today = new \DateTime();
            if (/*$reservation->getStartDate()->getTimestamp() > $today->getTimestamp() && */ $reservation->getStartDate()->diff($today)->days > 2) {
                $viewData = [
                    'reservation' => $reservation,
                ];
                return $this->render('cancelReservation/reservation-cancel-confirm.html.twig', $viewData);
            }
        }
        $this->flashBag->add('danger', 'Too late to cancel');
        return $this->redirectToRoute('reservation_cancel');
    }

    /**
     * @Route("/confirmed", name="cancel_confirmed")
     * @param Request $request
     * @return RedirectResponse
     */
    public function cancelReservationConfirmed(Request $request)
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reservation = $form->getData();
            $this->deleteReservation($reservation);

            $this->flashBag->add('success', 'reservation cancelled!');
        } else {
            $this->flashBag->add('danger', 'Something went wrong');
        }

        return $this->redirectToRoute('cancel_reservation');
    }

    private function deleteReservation(Reservation $reservation)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $hotel = $reservation->getHotel();

        $hotelReservations = $hotel->getReservations();
        $from = new \DateTime();
        $from->setTimestamp($reservation->getStartDate()->getTimestamp());
        $to = $reservation->getEndDate();
        while ($from != $to) {
            $hotelReservations[$from->format('yyyy-MM-dd')]-= $reservation->getBeds() ;
            if ($hotelReservations[$from->format('yyyy-MM-dd')] == 0) {
                unset($hotelReservations[$from->format('yyyy-MM-dd')]);
            }
            $from = $from->modify("+1 day");
        }
        $hotel->setReservations($hotelReservations);

        $entityManager->persist($hotel);
        $entityManager->remove($reservation);
        $entityManager->flush();
    }
}
