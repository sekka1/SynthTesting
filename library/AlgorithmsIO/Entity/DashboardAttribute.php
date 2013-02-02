<?php

namespace AlgorithmsIO\Entity;
use Doctrine\ORM\Mapping AS ORM;
/**
 * DatSourceAttribs
 *
 * @ORM\Table(name="dashboardattribs")
 * @ORM\Entity
 */
require_once ("AlgorithmsIO/Entity/EntityAttributeBase.php");
class DashboardAttribute extends EntityAttributeBase {

    /**
     * @var integer $id
     *
     * ORM\Column(name="id", type="integer", nullable=false)
     * ORM\Id
     * ORM\GeneratedValue(strategy="IDENTITY")
     */
    //protected $id;    
    
    /** @ORM\Id @ORM\ManyToOne(targetEntity="Dashboards", inversedBy="attributes")
     */ // Removed (at)Id
    protected $dashboard;
    
    /** 
     * @ORM\Id @ORM\Column(type="string") // Removed (at)Id
     */
    protected $attribute;
    
    /** @ORM\Column(type="text") */
    protected $value;
    
    public function __construct($attribName, $value, $entity)
    {
        $this->attribute = $attribName;
        $this->value = $value;
        $this->dashboard = $entity;
    }    
}

?>
