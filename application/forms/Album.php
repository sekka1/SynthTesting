<?php 
class Form_Album extends Zend_Form 
{ 
    public function __construct($options = null) 
    { 
        parent::__construct($options);
        $this->setName('album'); 
        $id = new Zend_Form_Element_Hidden('id'); 
        $artist = new Zend_Form_Element_Text('artist'); 
        $artist->setLabel('Artist') 
               ->setRequired(true) 
               ->addFilter('StripTags') 
               ->addFilter('StringTrim') 
               ->addValidator('NotEmpty'); 
        $title = new Zend_Form_Element_Text('title'); 
        $title->setLabel('Title') 
              ->setRequired(true) 
              ->addFilter('StripTags') 
              ->addFilter('StringTrim') 
              ->addValidator('NotEmpty'); 
        $submit = new Zend_Form_Element_Submit('submit'); 
        $submit->setAttrib('id', 'submitbutton'); 
        $this->addElements(array($id, $artist, $title, $submit)); 
    } 
} 
?>
