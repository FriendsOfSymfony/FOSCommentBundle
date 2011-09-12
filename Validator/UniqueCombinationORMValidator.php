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
        
        $properties = (array)$value;
        $whereClause = array();
        reset($constraint->properties);
        foreach ($constraint->properties as $property) {
            if (!in_array($property, $properties) && !method_exists($value, "get$property") && !method_exists($value, "is$property")) {
                throw new \InvalidArgumentException(sprintf('$value does not contain either a public property "%1$s", a "get%2$s()" method or an "is%2$s()" method',
                    $property, ucfirst($property)));
            }
            $whereClause[] = sprintf('v.%1$s = :%1$s', $property);
        }
        $whereClause = implode(' AND ', $whereClause);
        
        $dql = sprintf('SELECT count(v.id) FROM %s v WHERE %s', $this->vote_class, $whereClause);
        $query = $this->em->createQuery($dql);
        
        reset($constraint->properties);
        foreach ($constraint->properties as $property) {
            $paramValue = null;
            if (method_exists($value, "get$property")) {
                $paramValue = $value->{"get$property"}();
            } else if (method_exists($value, "is$property")) {
                $paramValue = $value->{"is$property"}();
            } else {
                $paramValue = $value->$property;
            }
            $query->setParameter($property, $paramValue);
        }
        
        $count = (int)$query->getSingleScalarResult();

        if ($count > 0) {
            $this->setMessage($constraint->message);
            return false;
        }
        
        return true;
    }

}
