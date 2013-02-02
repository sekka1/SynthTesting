<?php


namespace AlgorithmsIO\Entity;
use Doctrine\ORM\Mapping AS ORM;
require_once ("AlgorithmsIO/Entity/EntityBase.php");
require_once ("AlgorithmsIO/Entity/CustomerAttribute.php");
/**
 *  
 * @ORM\Table(name="customers")
 * @ORM\Entity
 */
class Customers extends EntityBase
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    //protected $idSeq;

    /**
     * @ORM\OneToMany(targetEntity="Roles", mappedBy="customer", cascade={"ALL"}, indexBy="role_id")
     */
    protected $roles;
    
    /**
     *
     * @ORM\Column(name="name", type="string", length=256, nullable=true)
     */
    protected $name;
    
    /**
     *
     * @ORM\Column(name="longName", type="string", length=256, nullable=true)
     */
    protected $longName;

    /**
     * @var datetime $created
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @var datetime $last_modified
     *
     * @ORM\Column(name="last_modified", type="datetime", nullable=true)
     */
    protected $last_modified;

    /**
     * @ORM\OneToMany(targetEntity="CustomerAttribute", mappedBy="customer", cascade={"ALL"}, indexBy="attribute")
     */
    protected $attributes;


}