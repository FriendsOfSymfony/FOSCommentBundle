<?php

namespace FOS\CommentBundle\Validator;

use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use FOS\CommentBundle\Model\SignedVoteInterface;

class UniqueCombinationORMValidator extends ConstraintValidator
{
    
    protected $em;
    protected $vote_class;
    
    public function __construct(EntityManager $em, $vote_class)
    {
        $this->em = $em;
        $this->vote_class = $vote_class;
    }
    
    public function isValid($value, Constraint $constraint)
    {
        if (!$value instanceof SignedVoteInterface || null === $value->getVoter()) {
            $this->setMessage($constraint->message);
            return false;
        }
        
        $whereClause = array();
        reset($constraint->properties);
        foreach ($constraint->properties as $property) {
            $whereClause[] = sprintf('v.%1$s = :%1$s', $property);
        }
        $whereClause = implode(' AND ', $whereClause);
        
        $dql = sprintf('SELECT count(v.id) FROM %s v WHERE %s', $this->vote_class, $whereClause);
        $query = $this->em->createQuery($dql);
        
        reset($constraint->properties);
        foreach ($constraint->properties as $property) {
            $query->setParameter($property, $value->{"get$property"}());
        }
        
        $count = (int)$query->getSingleScalarResult();

        if ($count > 0) {
            $this->setMessage($constraint->message);
            return false;
        }
        
        return true;
    }

}
