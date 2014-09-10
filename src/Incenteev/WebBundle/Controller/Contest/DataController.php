<?php

namespace Incenteev\WebBundle\Controller\Contest;

use FOS\Rest\Util\Codes;
use Incenteev\WebBundle\Entity\DataEntry;
use Incenteev\WebBundle\Entity\Contest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Contest data controller.
 *
 */
class DataController extends BaseController
{
    public function showResultsAction($id)
    {
        $contest = $this->findContestForOwner($id, 'Only contest owner can edit other results.');

        return $this->render('WebBundle:Contest/Data:showResults.html.twig', array('contest' => $contest));
    }

    public function getDataEntriesAction($id)
    {
        $contest = $this->getContestRepository()->getContestWithParticipants($id);

        if (!$contest) {
            throw new NotFoundHttpException(sprintf('Unable to find contest with id %s.', $id));
        }

        $this->checkContestOwnerShip($contest, 'Only contests owners can view all result data of a contest.');

        /** @var $manager \Incenteev\WebBundle\Contest\DataEntryManager */
        $manager = $this->get('incenteev.contest.data_entry_manager');
        $interval = $manager->getGranularityInterval($contest);

        $startDate = $contest->getStartDate();
        $endDate = $contest->getEndDate();

        $periods = array_map(
            function (\DateTime $date) use ($interval, $startDate, $endDate) {
                $endPeriodDate = clone $date;
                $endPeriodDate->add($interval)->modify('-1 day');

                return array(
                    'timestamp' => $date->format('U'),
                    'start' => max($startDate, $date)->format('U'),
                    'end' => min($endDate, $endPeriodDate)->format('U'),
                );
            },
            $manager->getAllPastDates($contest)
        );

        $entries = $this->getDataEntryRepository()->getAllEntries($contest);
        $processedEntries = array();

        foreach ($entries as $entry) {
            $processedEntries[] = array(
                'id' => $entry->getId(),
                'date' => $entry->getDate()->format('U'),
                'participation' => $entry->getParticipation()->getId(),
                'value' => (float) $entry->getValue(),
            );
        }

        $participations = array();

        foreach ($this->getParticipationRepository()->getContestParticipations($contest) as $participation) {
            $participations[] = array(
                'id' => $participation->getId(),
                'username' => $participation->getUser()->getName(),
            );
        }

        return new JsonResponse(array(
            'periods' => $periods,
            'participations' => $participations,
            'entries' => $processedEntries,
        ));
    }

    public function saveDataEntryAction(Request $request, $id)
    {
        $contest = $this->getContestRepository()->getContestWithParticipants($id);

        $this->checkContestOwnerShip($contest, 'Only contest owner can edit other results.');

        if (!$contest->isPublished()) {
            throw new AccessDeniedException('Impossible to submit data to an unpublished contest.');
        }

        /** @var $csrfProvider \Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface */
        $csrfProvider = $this->get('form.csrf_provider');

        if (!$csrfProvider->isCsrfTokenValid('save_entry', $request->request->get('_token'))) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Invalid CSRF token'), Codes::HTTP_PRECONDITION_FAILED);
        }

        /** @var $manager \Incenteev\WebBundle\Contest\DataEntryManager */
        $manager = $this->get('incenteev.contest.data_entry_manager');
        $allowedDates = $manager->getAllPastDates($contest);

        $changes = $request->request->get('data', array());

        $errors = array();
        $dates = array();
        $participations = array();
        $participationIds = array();

        foreach ($changes as $key => $changedEntry) {
            if (!isset($changedEntry['value'])) {
                $errors[$key] = array(
                    'message' => 'The value is missing.',
                    'type' => 'missing_value',
                );
                continue;
            }

            if (!isset($changedEntry['date'])) {
                $errors[$key] = array(
                    'message' => 'The date is missing.',
                    'type' => 'missing_date',
                );
                continue;
            }

            $date = new \DateTime('@'.$changedEntry['date']);
            if (!in_array($date, $allowedDates)) {
                $errors[$key] = array(
                    'message' => 'This date is not associated to values in this contest.',
                    'type' => 'invalid_date',
                );
                continue;
            }

            if (!isset($changedEntry['participation'])) {
                $errors[$key] = array(
                    'message' => 'The participation is missing.',
                    'type' => 'missing_participation',
                );
                continue;
            }

            foreach ($contest->getParticipations() as $participation) {
                if ($participation->getId() === (int) $changedEntry['participation']) {
                    $participations[$key] = $participation;
                    $participationIds[$key] = $participation->getId();
                    break;
                }
            }

            if (!isset($participations[$key])) {
                $errors[$key] = array(
                    'message' => 'This participation does not exists in the contest.',
                    'type' => 'invalid_participation',
                );
                continue;
            }

            $dates[$key] = $date;
        }

        /** @var $entries DataEntry[] */
        $entries = $this->getDataEntryRepository()->findByDatesAndParticipations(array_values($dates), array_values($participationIds));

        $em = $this->getDoctrine()->getManager();

        foreach ($changes as $key => $changedEntry) {
            if (!empty($errors[$key])) {
                continue;
            }

            $foundEntry = null;

            foreach ($entries as $entry) {
                if ($entry->getParticipation() === $participations[$key] && $entry->getDate() == $dates[$key]) {
                    $foundEntry = $entry;
                    break;
                }
            }

            if (null === $foundEntry) {
                $foundEntry = new DataEntry();
                $foundEntry->setDate($dates[$key])
                    ->setParticipation($participations[$key]);
            }

            $value = trim($changedEntry['value']);

            if ('' === $value) {
                $em->remove($foundEntry);
                continue;
            }

            if (!preg_match('/\d/', $value)) {
                $errors[$key] = array(
                    'message' => 'This value is invalid.',
                    'type' => 'invalid_value',
                );
                continue;
            }

            $cleanedValue = preg_replace('/^[^\d\.]+/', '', str_replace(',', '.', $value));

            $castedValue = (float) $cleanedValue;

            $foundEntry->setValue($castedValue);
            $em->persist($foundEntry);
        }

        $em->flush();

        if (empty($errors)) {
            return new JsonResponse(array('status' => 'success'));
        }

        $errors = new \ArrayObject($errors);

        if (count($errors) === count($changes)) {
            return new JsonResponse(array('status' => 'error', 'errors' => $errors), 400);
        }

        return new JsonResponse(array('status' => 'partial', 'errors' => $errors));
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\DataEntryRepository
     */
    private function getDataEntryRepository()
    {
        return $this->getDoctrine()->getRepository('WebBundle:DataEntry');
    }
}
