<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Absence;
use AppBundle\Entity\TimeEntry;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CalendarController
 *
 * @package AppBundle\Controller
 */
class CalendarController extends Controller
{
    /**
     * @return array
     *
     * @Method("GET")
     * @Route("/", name="calendar_index")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Method("GET")
     * @Route("/calendar", name="calendar_events", options={"expose"=true})
     * @Security("is_granted('VIEW', user.getActiveTeam())")
     */
    public function calendarEventsAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $startDate = new \DateTime($request->query->get('start', 'now'));
        $startDate->setTime(0, 0);
        $endDate = new \DateTime($request->query->get('end', 'now'));
        $endDate->setTime(0, 0);

        $timeEntryResult = $this->getDoctrine()->getManager()->getRepository('AppBundle:TimeEntry')->findByTeamAndUserQuery(
            $user->getActiveTeam(),
            $user,
            $startDate,
            $endDate)->getResult();
        $absenceResult = $this->getDoctrine()->getManager()->getRepository('AppBundle:Absence')->findByTeamAndUserQuery(
            $user->getActiveTeam(),
            $user,
            $startDate,
            $endDate)->getResult();

        $return = [];

        /** @var TimeEntry $entry */
        foreach ($timeEntryResult as $entry) {
            $return[] = [
                'id' => 'entry_' . $entry->getId(),
                'title' => $entry->getNote(),
                'start' => $entry->getDateTimeRange()->getStartDateTime()->format('c'),
                'end' => $entry->getDateTimeRange()->getEndDateTime()->format('c'),
                'allDay' => false,
                'className' => 'entry'
            ];
        }

        /** @var Absence $entry */
        foreach ($absenceResult as $entry) {
            $return[] = [
                'id' => 'absence_' . $entry->getId(),
                'title' => $entry->getNote(),
                'start' => $entry->getDateTimeRange()->getStartDateTime()->format('c'),
                'end' => $entry->getDateTimeRange()->getEndDateTime()->format('c'),
                'allDay' => false,
                'className' => 'absence'
            ];
        }

        return new JsonResponse($return);
    }

    /**
     * @Method("GET")
     * @Route("/statistics", name="calendar_statistics", options={"expose"=true})
     * @Security("is_granted('VIEW', user.getActiveTeam())")
     * @param Request $request
     * @return JsonResponse
     */
    public function statisticsAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $startDate = new \DateTime($request->query->get('start', 'now'));
        $startDate->setTime(0, 0);
        $endDate = new \DateTime($request->query->get('end', 'now'));
        $endDate->setTime(0, 0);

        $timeEntryResult = $this->getDoctrine()->getManager()->getRepository('AppBundle:TimeEntry')->findByTeamAndUserQuery(
            $user->getActiveTeam(),
            $user,
            $startDate,
            $endDate)->getResult();
        $absenceResult = $this->getDoctrine()->getManager()->getRepository('AppBundle:Absence')->findByTeamAndUserQuery(
            $user->getActiveTeam(),
            $user,
            $startDate,
            $endDate)->getResult();

        $return = [];

        /** @var TimeEntry $entry */
        foreach ($timeEntryResult as $entry) {
            $date = $entry->getDateTimeRange()->getDate()->format('Y-m-d');
            if (!isset($return[$date])) {
                $statistic = ['entry' => 0];
                $return[$date] = $statistic;
            }
            $return[$date]['entry'] += $entry->getDateTimeRange()->getInterval()->h;
            $return[$date]['entry'] += ($entry->getDateTimeRange()->getInterval()->i) / 60;
        }

        /** @var Absence $entry */
        foreach ($absenceResult as $entry) {
            $date = $entry->getDateTimeRange()->getDate()->format('Y-m-d');
            if (!isset($return[$date])) {
                $statistic = [
                    'absence' => 0
                ];
                $return[$date] = $statistic;
            }
            if (!isset($return[$date]['absence'])) {
                $statistic = $return[$date];
                $statistic['absence'] = 0;
                $return[$date] = $statistic;
            }
            $return[$date]['absence'] += $entry->getDateTimeRange()->getInterval()->h;
            $return[$date]['absence'] += ($entry->getDateTimeRange()->getInterval()->i) / 60;
        }

        return new JsonResponse($return);
    }
}
