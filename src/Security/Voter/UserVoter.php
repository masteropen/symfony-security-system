<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter Authorization mechanism
 */
class UserVoter extends Voter
{

    const ROLE_USER = 'ROLE_USER';

    const ROLE_ADMIN = 'ROLE_ADMIN';

    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * Check if this voter support given attribute and subject.
     * This function will be invoked when you call $this->>denyAccessUnlessGranted("attribute", $user) in controller.
     *
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    public function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::ROLE_USER, self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * Vote if user can execute $attribute action or if have specific role attribute.
     * If one voter refuse given access to user, the controller will return automatically
     * a Response with Response::HTTP_FORBIDDEN status code (Access denied).
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch ($attribute) {
            case self::ROLE_USER:
                return $subject === $user && in_array(self::ROLE_USER, $user->getRoles());
                break;
            case self::ROLE_ADMIN:
                return true;
                break;
            default:
                return false;
        }

        throw new \LogicException('This code should not be reached!');
    }
}
