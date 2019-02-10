<?php
// src/User.php
/**
 * @Entity @Table(name="users")
 **/
class User
{
   /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     **/
    protected $id;
 
    /**
     * @Column(type="string")
     * @var string
     **/
    protected $name;
 
    /**
     * @Column(type="string")
     * @var string
     **/
    protected $surname;
 
     /**
     * @Column(type="string", unique=true)
     * @var string
     **/
    protected $email;
 
    public function getId()
    {
        return $this->id;
    }
 
    public function getName()
    {
        return $this->name;
    }
 
    public function setName($name)
    {
        $this->name = $name;
    }
 
    public function getSurname()
    {
        return $this->surname;
    }
 
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }
 
    public function getEmail()
    {
        return $this->price;
    }
 
    public function setEmail($email)
    {
        $this->email = $email;
    }
}