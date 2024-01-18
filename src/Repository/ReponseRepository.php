<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Reponse;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reponse>
 *
 * @method Reponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reponse[]    findAll()
 * @method Reponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reponse::class);
    }

    /**
     * Permet de savoir si un utilisateur a déjà voté pour une réponse sous une question
     */
    public function hasVoted(User $user, Question $question): bool
    {
        $results = $this->createQueryBuilder('reponse') // SELECT * FROM reponse
            ->innerJoin('reponse.voters', 'voter')// 1er : propropriété de l'entity réponse pour accéder à la table associative, 2eme : alias qu'on lui donne pour ensuite accéder à la table associative. // INNER JOIN user_reponse ON user_reponse.reponse_id = reponse.id
            ->where('voter.id = :user') // WHERE user_reponse.user_id = ?
            ->andWhere('reponse.question = :question') // AND reponse.question_id = ?
            ->setParameter('user', $user) // On met pas les :, ça le recherche automatiquement
            ->setParameter('question', $question) // On met pas les :, ça le recherche automatiquement
            ->getQuery() // equivalent d'execute
            ->getResult(); // retourne les résultats trouvés

            return count($results) > 0;
    }

//    /**
//     * @return Reponse[] Returns an array of Reponse objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reponse
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
