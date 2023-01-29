<?php

namespace App\Controller\Admin;

use App\Entity\Settings;
use App\Form\SettingsType;
use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/settings')]
class SettingsController extends AbstractController
{
    #[Route('/', name: 'app_settings_index', methods: ['GET'])]
    public function index(SettingsRepository $settingsRepository): Response
    {
        return $this->render('settings/index.html.twig', [
            'settings' => $settingsRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_settings_show', methods: ['GET'])]
    public function show(Settings $setting): Response
    {
        return $this->render('settings/show.html.twig', [
            'setting' => $setting,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Settings $setting, SettingsRepository $settingsRepository): Response
    {
        $form = $this->createForm(SettingsType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $settingsRepository->save($setting, true);

            return $this->redirectToRoute('app_settings_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('settings/edit.html.twig', [
            'setting' => $setting,
            'form' => $form,
        ]);
    }
}
