<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Application\Dto\CreateContactDto;
use App\Application\Dto\UpdateContactDto;
use App\Domain\Exception\ContactNotFoundException;
use App\Domain\Exception\DuplicateEmailException;
use App\Application\UseCase\CreateContactUseCase;
use App\Application\UseCase\DeleteContactUseCase;
use App\Application\UseCase\GetContactUseCase;
use App\Application\UseCase\ListContactsUseCase;
use App\Application\UseCase\UpdateContactUseCase;
use App\Domain\Exception\InvalidEmailException;
use App\Domain\Exception\InvalidNameException;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class ContactController
{
    public function __construct(
        private readonly CreateContactUseCase $createContact,
        private readonly DeleteContactUseCase $deleteContact,
        private readonly GetContactUseCase $getContact,
        private readonly ListContactsUseCase $listContacts,
        private readonly UpdateContactUseCase $updateContact
    ) {}

    /**
     * GET /api/v1/contacts
     */
    public function index(Request $request): Response
    {
        $contacts = $this->listContacts->execute();

        $data = array_map(
            fn ($contact) => $contact->toArray(),
            $contacts
        );

        return Response::json($data);
    }

    /**
     * GET /api/v1/contacts/{id}
     */
    public function show(Request $request): Response
    {
        $id = $request->routeParam('id');

        $contact = $this->getContact->execute($id);

        return Response::json($contact->toArray());
    }

    /**
     * POST /api/v1/contacts
     */
    public function store(Request $request): Response
    {
        try {
            $dto = CreateContactDto::fromArray($request->body());

            $contact = $this->createContact->execute($dto);

            return Response::created($contact->toArray());

        } catch (DuplicateEmailException $e) {
            return Response::conflict($e->getMessage());

        } catch (
            InvalidEmailException |
            InvalidNameException $e
        ) {
            return Response::unprocessableEntity([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * PUT /api/v1/contacts/{id}
     */
    public function update(Request $request): Response
    {
        try {
            $id = $request->routeParam('id');

            $dto = UpdateContactDto::fromRequest(
                $id,
                $request->body()
            );

            $contact = $this->updateContact->execute($dto);

            return Response::json($contact->toArray());

        } catch (ContactNotFoundException $e) {
            return Response::notFound($e->getMessage());

        } catch (DuplicateEmailException $e) {
            return Response::conflict($e->getMessage());

        } catch (
            InvalidEmailException |
            InvalidNameException $e
        ) {
            return Response::unprocessableEntity([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * DELETE /api/v1/contacts/{id}
     */
    public function destroy(Request $request): Response
    {
        try {
            $id = $request->routeParam('id');

            $this->deleteContact->execute($id);

            return Response::noContent();

        } catch (ContactNotFoundException $e) {
            return Response::notFound($e->getMessage());
        }
    }
}