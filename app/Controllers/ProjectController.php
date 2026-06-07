<?php

namespace App\Controllers;

use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class ProjectController
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private ProjectRepositoryInterface $projects
    ) {
    }

    public function manage(Request $request): Response
    {
        return $this->responseFactory->view('projects/manage.html.twig', [
            'projects' => $this->projects->findAll(),
            'active'   => 'manageProjects',
        ]);
    }

    public function showCreate(Request $request): Response
    {
        return $this->responseFactory->view('projects/create.html.twig');
    }

    public function create(Request $request): Response
    {
        $name        = $request->get('name');
        $description = $request->get('description');

        if (!$name || !$description) {
            return $this->responseFactory->internalError();
        }

        $project              = new Project();
        $project->name        = $name;
        $project->description = $description;
        $project->url         = $request->get('url') ?: null;
        $project->sort_order  = (int) ($request->get('sort_order') ?? 0);
        $project->created_at  = time();

        $this->projects->create($project);

        return $this->responseFactory
            ->createToast('success', 'Project aangemaakt!')
            ->redirect('/projects/manage');
    }

    public function showEdit(Request $request): Response
    {
        $project = $this->projects->findById((int) $request->get('id'));

        if (!$project) {
            return $this->responseFactory->notFound();
        }

        return $this->responseFactory->view('projects/edit.html.twig', [
            'project' => $project,
        ]);
    }

    public function update(Request $request): Response
    {
        $project = $this->projects->findById((int) $request->get('id'));

        if (!$project) {
            return $this->responseFactory->notFound();
        }

        $name        = $request->get('name');
        $description = $request->get('description');

        if (!$name || !$description) {
            return $this->responseFactory->internalError();
        }

        $project->name        = $name;
        $project->description = $description;
        $project->url         = $request->get('url') ?: null;
        $project->sort_order  = (int) ($request->get('sort_order') ?? $project->sort_order);

        $this->projects->update((int) $request->get('id'), $project);

        return $this->responseFactory
            ->createToast('success', 'Project bijgewerkt!')
            ->redirect('/projects/manage');
    }

    public function delete(Request $request): Response
    {
        if (!$this->projects->delete((int) $request->get('id'))) {
            return $this->responseFactory->internalError();
        }

        return $this->responseFactory
            ->createToast('info', 'Project verwijderd.')
            ->redirect('/projects/manage');
    }
}
