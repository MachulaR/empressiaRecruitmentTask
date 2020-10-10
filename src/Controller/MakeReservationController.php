<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reservation", name="reservation_")
 */
class MakeReservationController extends AbstractController
{
    /** @var FlashBagInterface */
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
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

    /**
     * @Route("/confirm", name="confirm")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function confirmReservation(Request $request)
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Reservation $reservationToConfirm */
            $reservationToConfirm = $form->getData();
            if ($this->isReservationPossibleToMake($reservationToConfirm)) {

                $reservationToConfirm->setTotalPrice($reservationToConfirm->calculatePrice());

                $viewData = [
                    'reservation' => $reservationToConfirm,
                ];
                return $this->render('makeReservation/reservation-confirm.html.twig', $viewData);
            }

        }
        $this->flashBag->add('danger', 'Something went wrong');
        return $this->redirectToRoute('reservation_index');
    }

    /**
     * @Route("/confirmed", name="make")
     * @param Request $request
     * @return RedirectResponse
     */
    public function makeReservation(Request $request)
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Reservation $reservation */
            $reservationToMake = $form->getData();
            $this->createReservation($reservationToMake);

            $this->flashBag->add('success', 'reservation completed!');
        } else {
            $this->flashBag->add('danger', 'Something went wrong');
        }

        return $this->redirectToRoute('reservation_index');
    }

    /**
     * @param Reservation $reservationToConfirm
     * @return bool
     */
    private function isReservationPossibleToMake(Reservation $reservationToConfirm)
    {
        $reservations = $reservationToConfirm->getHotel()->getReservations();

        $from = new \DateTime();
        $from->setTimestamp($reservationToConfirm->getStartDate()->getTimestamp());
        $to = $reservationToConfirm->getEndDate();
        while ($from != $to) {
            if (!key_exists($from->format('yyyy-MM-dd'), (array)$reservations)) {
                $reservations[$from->format('yyyy-MM-dd')] = 0;
            }

            if ($reservationToConfirm->getBeds() > $reservationToConfirm->getHotel()->getBeds() - $reservations[$from->format('yyyy-MM-dd')]) {
                return false;
            }
            $from->modify("+1 day");
        }
        return true;
    }

    private function createReservation(Reservation $reservation)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $hotel = $reservation->getHotel();

        $hotelReservations = $hotel->getReservations();
        $from = new \DateTime();
        $from->setTimestamp($reservation->getStartDate()->getTimestamp());
        $to = $reservation->getEndDate();
        while ($from != $to) {
            if (!key_exists($from->format('yyyy-MM-dd'), (array)$hotelReservations)) {
                $hotelReservations[$from->format('yyyy-MM-dd')] = 0;
            }
            $hotelReservations[$from->format('yyyy-MM-dd')] += $reservation->getBeds() ;

            $from = $from->modify("+1 day");
        }
        $hotel->setReservations($hotelReservations);

        $entityManager->persist($hotel);
        $entityManager->persist($reservation);
        $entityManager->flush();
    }
}
