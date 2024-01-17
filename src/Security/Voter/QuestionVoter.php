<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class QuestionVoter extends Voter
{
    public const ADD = 'QUESTION_ADD';

    public function __construct(
        private Security $security)
    { 
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Si l'attribut correspond à la valeur de la constante "ADD", on applique les règles du voter
        if ($attribute === self::ADD) {
            return true;
        }

        return in_array($attribute, [self::ADD])
            && $subject instanceof \App\Entity\Question;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ADD:
                return $this->security->isGranted('ROLE_USER');
                // logic to determine if the user can EDIT
                // return true or false
                break;
        }

        return false;
    }
}
