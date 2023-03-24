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

#[Route('/contacts')]
class ContactsController extends AbstractController
{
    #[Route('/', name: 'app_texts_index_contacts', methods: ['GET'])]
    public function index(TextsRepository $textsRepository): Response
    {
        return $this->render('contacts/index.html.twig', [
            'texts' => $textsRepository->findBy(['type' => TextsTypeEnum::CONTACTS->value]),
        ]);
    }

    #[Route('/{id}', name: 'app_texts_show_contacts', methods: ['GET'])]
    public function show(Texts $text): Response
    {
        if($text->getType() != TextsTypeEnum::CONTACTS->value){
            throw new ApiException(message: 'Сущность не найдена', status: Response::HTTP_NOT_FOUND);
        }
        return $this->render('contacts/show.html.twig', [
            'text' => $text,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_texts_edit_contacts', methods: ['GET', 'POST'])]
    public function edit(Request $request, Texts $text, TextsRepository $textsRepository): Response
    {
        if($text->getType() != TextsTypeEnum::CONTACTS->value){
            throw new ApiException(message: 'Сущность не найдена', status: Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(TextsType::class, $text, ['type' => TextsTypeEnum::CONTACTS->value]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $textsRepository->save($text, true);

            return $this->redirectToRoute('app_texts_index_contacts', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contacts/edit.html.twig', [
            'text' => $text,
            'form' => $form,
        ]);
    }
}
