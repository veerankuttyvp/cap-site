<?php
namespace CAP\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

/**
 * UserRepository
 *
 * Repository class to extend Doctrine ORM functions with your own
 * using DQL language. More here http://mackstar.com/blog/2010/10/04/using-repositories-doctrine-2
 *
 */
class UserRepository extends EntityRepository {
    public function youtCustomDQLFunction($number = 30) {

    }
}
