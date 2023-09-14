<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;  // Make sure to import this
use Symfony\Component\HttpFoundation\JsonResponse;

class TimeTrackerController extends AbstractController
{
    /**
     * @Route("/", name="time_tracker")
     */
    public function index(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle form submission and start timer logic here
        }

        return $this->render('time_tracker.html.twig', [
            'form' => $form->createView(),
        ]);
    }
	
	/**
	 * @Route("/api/insert-task", name="insert_task", methods={"POST"})
	 */
	public function insertTask(Request $request, EntityManagerInterface $entityManager): Response
	{
		$data = json_decode($request->getContent(), true);

		$task = new Task();
		$task->setName($data['name']);
		$task->setTime($data['time']);
		$task->setStartTime(new \DateTime($data['startTime']));
		$task->setEndTime(new \DateTime($data['endTime']));
		$task->setStatus($data['status']);
		$entityManager->persist($task);
		$entityManager->flush();

		return new JsonResponse(['status' => 'success']);
	}
}


