<?php

namespace App\Controller;

use App\Entity\Cigarettes;
use App\Form\CigarettesType;
use App\Repository\CigarettesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cigarettes')]
class CigarettesController extends AbstractController
{
    #[Route('/', name: 'app_cigarettes_index', methods: ['GET'])]
    public function index(CigarettesRepository $cigarettesRepository): Response
    {
        return $this->render('cigarettes/index.html.twig', [
            'cigarettes' => $cigarettesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cigarettes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CigarettesRepository $cigarettesRepository): Response
    {
        $cigarette = new Cigarettes();
        $form = $this->createForm(CigarettesType::class, $cigarette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cigarettesRepository->add($cigarette, true);

            return $this->redirectToRoute('app_cigarettes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cigarettes/new.html.twig', [
            'cigarette' => $cigarette,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cigarettes_show', methods: ['GET'])]
    public function show(Cigarettes $cigarette): Response
    {
        return $this->render('cigarettes/show.html.twig', [
            'cigarette' => $cigarette,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cigarettes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cigarettes $cigarette, CigarettesRepository $cigarettesRepository): Response
    {
        $form = $this->createForm(CigarettesType::class, $cigarette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cigarettesRepository->add($cigarette, true);

            return $this->redirectToRoute('app_cigarettes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cigarettes/edit.html.twig', [
            'cigarette' => $cigarette,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cigarettes_delete', methods: ['POST'])]
    public function delete(Request $request, Cigarettes $cigarette, CigarettesRepository $cigarettesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cigarette->getId(), $request->request->get('_token'))) {
            $cigarettesRepository->remove($cigarette, true);
        }

        return $this->redirectToRoute('app_cigarettes_index', [], Response::HTTP_SEE_OTHER);
    }
}
