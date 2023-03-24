<?php

namespace App\Controller\Admin;

use App\Entity\Texts;
use App\Form\TextsType;
use App\Helper\EnumType\TextsType as TextsTypeEnum;
use App\Helper\Exception\ApiException;
use App\Repository\TextsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/how-buy')]
class HowBuyController extends AbstractController
{
    #[Route('/', name: 'app_texts_index_how_buy', methods: ['GET'])]
    public function index(TextsRepository $textsRepository): Response
    {
        return $this->render('howBuy/index.html.twig', [
            'texts' => $textsRepository->findBy(['type' => TextsTypeEnum::HOW_TO_BUY->value]),
        ]);
    }

    #[Route('/new', name: 'app_texts_new_how_buy', methods: ['GET', 'POST'])]
    public function new(Request $request, TextsRepository $textsRepository): Response
    {
        $text = (new Texts())
            ->setType(TextsTypeEnum::HOW_TO_BUY->value);
        $form = $this->createForm(TextsType::class, $text, ['type' => TextsTypeEnum::HOW_TO_BUY->value]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $textsRepository->save($text, true);

            return $this->redirectToRoute('app_texts_index_how_buy', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('howBuy/new.html.twig', [
            'text' => $text,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_texts_edit_how_buy', methods: ['GET', 'POST'])]
    public function edit(Request $request, Texts $text, TextsRepository $textsRepository): Response
    {
        if($text->getType() != TextsTypeEnum::HOW_TO_BUY->value){
            throw new ApiException(message: 'Сущность не найдена', status: Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(TextsType::class, $text, ['type' => TextsTypeEnum::HOW_TO_BUY->value]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $textsRepository->save($text, true);

            return $this->redirectToRoute('app_texts_index_how_buy', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('howBuy/edit.html.twig', [
            'text' => $text,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_texts_show_how_buy', methods: ['GET'])]
    public function show(Texts $text): Response
    {
        if($text->getType() != TextsTypeEnum::HOW_TO_BUY->value){
            throw new ApiException(message: 'Сущность не найдена', status: Response::HTTP_NOT_FOUND);
        }
        return $this->render('howBuy/show.html.twig', [
            'text' => $text,
        ]);
    }

    #[Route('/{id}', name: 'app_texts_delete_how_buy', methods: ['POST'])]
    public function delete(Request $request, Texts $text, TextsRepository $textsRepository): Response
    {
        if($text->getType() != TextsTypeEnum::HOW_TO_BUY->value){
            throw new ApiException(message: 'Сущность не найдена', status: Response::HTTP_NOT_FOUND);
        }
        if ($this->isCsrfTokenValid('delete'.$text->getId(), $request->request->get('_token'))) {
            $textsRepository->remove($text, true);
        }

        return $this->redirectToRoute('app_texts_index_how_buy', [], Response::HTTP_SEE_OTHER);
    }

}
