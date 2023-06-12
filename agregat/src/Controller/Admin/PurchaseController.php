<?php

namespace App\Controller\Admin;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/purchase')]
class PurchaseController extends AbstractController
{
    #[Route('/', name: 'app_purchase_index', methods: ['GET'])]
    public function index(PurchaseRepository $purchaseRepository): Response
    {
        return $this->render('purchase/index.html.twig', [
            'purchases' => $purchaseRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_show', methods: ['GET'])]
    public function show(Purchase $purchase): Response
    {
        return $this->render('purchase/show.html.twig', [
            'purchase' => $purchase,
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_delete', methods: ['POST'])]
    public function delete(Request $request, Purchase $purchase, PurchaseRepository $purchaseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$purchase->getId(), $request->request->get('_token'))) {
            $purchaseRepository->remove($purchase, true);
        }

        return $this->redirectToRoute('app_purchase_index', [], Response::HTTP_SEE_OTHER);
    }
}
