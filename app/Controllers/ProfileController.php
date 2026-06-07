<?php

namespace App\Controllers;

use App\Repositories\ProfileRepositoryInterface;
use App\Repositories\ProjectRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class ProfileController
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private ProfileRepositoryInterface $profiles,
        private ProjectRepositoryInterface $projectRepository
    ) {
    }

    public function index(Request $request): Response
    {
        $profile = $this->profiles->get();

        if (!$profile) {
            return $this->responseFactory->view('404.html.twig');
        }

        $age = null;

        if ($profile->birthdate) {
            $birthdate = new \DateTime($profile->birthdate);
            $age = $birthdate->diff(new \DateTime())->y;
        }

        return $this->responseFactory->view('profile.html.twig', [
            'active'    => 'profile',
            'profile'   => $profile,
            'skills'    => explode('|', $profile->skills),
            'traits'    => explode('|', $profile->traits),
            'age'       => $age,
            'projects'  => $this->projectRepository->findAll(),
        ]);
    }

    public function update(Request $request): Response
    {
        $profile = $this->profiles->get();

        if (!$profile) {
            return $this->responseFactory->internalError();
        }

        $profile->intro      = $request->get('intro') ?? $profile->intro;
        $profile->bio        = $request->get('bio') ?? $profile->bio;

        $profile->birthdate  = $request->get('birthdate');

        $profile->education  = $request->get('education');
        $profile->experience = $request->get('experience');

        $profile->github     = $request->get('github');
        $profile->linkedin   = $request->get('linkedin');
        $profile->spotify    = $request->get('spotify');
        $profile->discord    = $request->get('discord');

        $profile->image      = $request->get('image');

        $skillsRaw = $request->get('skills') ?? '';

        $profile->skills = implode('|', array_filter(
            array_map('trim', explode("\n", $skillsRaw))
        ));

        $traitsRaw = $request->get('traits') ?? '';

        $profile->traits = implode('|', array_filter(
            array_map('trim', explode("\n", $traitsRaw))
        ));

        $this->profiles->update($profile);

        return $this->responseFactory
        ->createToast('success', 'Succesvol aangepast!')
        ->redirect('/profile');
    }

    public function edit(Request $request): Response
    {
        $profile = $this->profiles->get();

        if (!$profile) {
            return $this->responseFactory->internalError();
        }

        return $this->responseFactory->view('user/profile-edit.html.twig', [
            'profile' => $profile,
            'skills'  => explode('|', $profile->skills),
            'traits'  => explode('|', $profile->traits),
            'active' => 'editProfile',
        ]);
    }
}
