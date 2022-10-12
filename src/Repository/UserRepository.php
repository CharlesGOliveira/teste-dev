<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Exception;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method User[]    save($user)
 * @method User[]    getUserByCpf($cpf)
 * @method User[]    update($user)
 * @method User[]    delete($user)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Persiste os dados do usuário na base de dados
     * 
     * @param User $user
     * @param ObjectManager $manager
     * 
     * @return array
     */
    public function save($user, ObjectManager $manager)
    {
        $manager->persist($user);
        $manager->flush();

        return $this->buildDefaultReturn(200, 'Usuário cadastrado com sucesso.');
    }

    /**
     * Retorna o usuário com base no cpf informado
     * 
     * @param string $cpf
     * 
     * @return User
     * 
     * @throws Exception
     */
    public function getUserByCpf($cpf)
    {
        $user = $this->findOneBy(['cpf' => $cpf]);

        if (empty($user)) {
            throw new Exception('Usuário não encontrado com base no CPF informado.', 404);
        }

        return $user;
    }

    /**
     * Atualiza o usuário na base de dados
     * 
     * @param User $user
     * @param ObjectManager $manager
     * 
     * @return array
     */
    public function update($user, ObjectManager $manager)
    {
        $manager->merge($user);
        $manager->flush();

        return $this->buildDefaultReturn(200, 'Usuário atualizado com sucesso.');
    }

    /**
     * Exclui o usuário na base de dados
     * 
     * @param User $user
     * @param ObjectManager $manager
     * 
     * @return array
     */
    public function delete($user, ObjectManager $manager)
    {
        $manager->remove($user);
        $manager->flush();

        return $this->buildDefaultReturn(200, 'Usuário excluído com sucesso.');
    }

    /**
     * Constroí o retorno para a controller
     * 
     * @param int $code
     * @param string $message
     * 
     * @return array
     */
    private function buildDefaultReturn($code, $message)
    {
        return [
            'code' => $code,
            'message' => $message
        ];
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
