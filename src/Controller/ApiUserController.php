<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\Redis;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/user", defaults={"_format": "json"})
 */
class ApiUserController extends AbstractController
{
    /**
     * @Route("/getall", name="api_getall_user", methods={"GET"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     *
     * @throws \JsonException
     * @throws \Exception
     */
    public function getall(EntityManagerInterface $em, Request $request): Response
    {
        $users = $em->getRepository(User::class)->findAll();

        $response = [];
        foreach ($users as $user) {
            $response[] = [
                'Id' => $user->getId(),
                'Name' => $user->getName(),
                'Age' => $user->getAge(),
                'City' => $user->getCity(),
                'Cpf' => $user->getCpf()
            ];
        }

        return $this->setResponse($response);
    }

    /**
     * @Route("", name="api_create_user", methods={"POST"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     *
     * @throws \JsonException
     * @throws \Exception
     */
    public function create(Request $request, EntityManagerInterface $em)
    {
        try {
            $data = json_decode($request->getContent(), true);

            $this->validateInfosToCreate($data);

            $user = new User();
            $user->setName($data['name']);
            $user->setAge($data['age']);
            $user->setCity($data['city']);
            $user->setCpf($data['cpf']);

            $return = $em->getRepository(User::class)->save($user, $em);

        } catch (\Exception $e) {
            $return = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];

        } finally {
            return $this->setResponse($return);
        }
    }

    /**
     * Verifica se os campos obrigatórios foram informados na requisição.
     * 
     * @param array $data
     * 
     * @return bool
     * 
     * @throws \Exception
     */
    private function validateInfosToCreate(array $data)
    {
        $requiredFields = [
            'name',
            'age',
            'city',
            'cpf'
        ];

        foreach ($requiredFields as $required) {
            if (empty($data[$required])) {
                throw new Exception('Requisição inválida. ' . $required . ' é obrigatório.', 400);
            }
        }

        return; 
    }

    /**
     * @Route("/{cpf}", name="api_read_user", methods={"GET"})
     *
     * @param string $cpf
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     *
     * @throws \JsonException
     * @throws \Exception
     */
    public function read(string $cpf, EntityManagerInterface $em)
    {
        try {
            $user = $em->getRepository(User::class)->getUserByCpf($cpf);

            $return = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'age' => $user->getAge(),
                'city' => $user->getCity(),
                'cpf' => $user->getCpf()
            ];

        } catch (\Exception $e) {
            $return = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];

        } finally {
            return $this->setResponse($return);
        }
    }

    /**
     * @Route("/{cpf}", name="api_update_user", methods={"PUT"})
     *
     * @param string $cpf
     * @param Request $request
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     *
     * @throws \JsonException
     * @throws \Exception
     */
    public function update(string $cpf, Request $request, EntityManagerInterface $em)
    {
        try {
            $request = json_decode($request->getContent(), true);

            $data = $em->getRepository(User::class)->getUserByCpf($cpf);
    
            $user = new User();
            $user->setId($data->getId());
            $user->setName($data->getName());
            $user->setAge($data->getAge());
            $user->setCity($data->getCity());
            $user->setCpf($data->getCpf());
    
            $user = $this->validateInfosToUpdate($user, $request);
    
            $return = $em->getRepository(User::class)->update($user, $em);

        } catch (\Exception $e) {
            $return = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        } finally {
            return $this->setResponse($return);
        }
    }

    /**
     * Verifica quais campos foram informados na requisição para atualizar o usuário
     * 
     * @param User $user
     * @param array $request
     * 
     * @return void
     */
    private function validateInfosToUpdate(User &$user, array $request)
    {
        if (!empty($request['name'])) {
            $user->setName($request['name']);
        }

        if (!empty($request['age'])) {
            $user->setAge($request['age']);
        }

        if (!empty($request['city'])) {
            $user->setCity($request['city']);
        }

        if (!empty($request['cpf'])) {
            $user->setCpf($request['cpf']);
        }

        return $user;
    }

    /**
     * @Route("/{cpf}", name="api_delete_user", methods={"DELETE"})
     *
     * @param string $cpf
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     *
     * @throws \JsonException
     * @throws \Exception
     */
    public function delete(string $cpf, EntityManagerInterface $em)
    {
        try {
            $user = $em->getRepository(User::class)->getUserByCpf($cpf);
    
            $return = $em->getRepository(User::class)->delete($user, $em);

        } catch (\Exception $e) {
            $return = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];

        } finally {
            return $this->setResponse($return);
        }
    }

    /**
     * Monta o retorno
     * 
     * @param array $return
     * 
     * @return JsonResponse
     */
    private function setResponse(array $return)
    {
        $response = new JsonResponse($return);
        $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );

        if (!empty($return['code'])) {
            $response->setStatusCode($return['code']);
        }

        return $response;
    }
}

