<?php
require_once('../classes/Connection.class.php');
require_once('../models/User.class.php');

Class Message
{
    public $id;
    public $user_id;
    public $content;

    public $errors = [];

    // STRONG MODEL & thin controller


	// un Object est une "instance" de Classe
	// un object est basé sur le moule

	/**
	 * User constructor. function native de php pour les classes
	 * @param null $id
	 */
	// $toto = new User() => User->__construct()
	// $toto = new User(25) => User->__construct(25)
	public function __construct($id = null)
    {
        if (!is_null($id)) {
        	// this fait reference a l'object, donc l'instance
            $this->get($id);
        }
    }

    public function get($id = null)
    {
        if (!is_null($id)) {
        	$dbh = Connection::get();
            //print_r($dbh);

            $stmt = $dbh->prepare("select * from messages where id = :id");
            $stmt->execute(array(
                ':id' => $id
            ));
            // recupere les users et fout le resultat dans une variable sous forme de tableau de tableaux
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Message');

            $message = $stmt->fetch();

            $this->id = $message->id;
            $this->user_id = $message->user_id;
            $this->content = $message->content;


        }
    }

    public function validate($data)
    {
        $this->errors = [];

        /* required fields */
        if (!isset($data['user_id'])) {
            $this->errors[] = 'champ user vide';

        }else{
	        $user = new User($data['user_id']);

	        if(!empty($user->id)){
	        	// un user existe avec cet id !
		        echo('YOUPI');
	        }else{
		        $this->errors[] = 'pas de user correspondant';
	        }
        }

        if (isset($data['content'])) {
            if (empty($data['content'])) {
                $this->errors[] = 'champ content vide';
                // si name > 50 chars
            } else if (mb_strlen($data['content']) < 8) {
                $this->errors[] = 'champ content trop court (8 min)';
            } else if (mb_strlen($data['content']) > 500) {
                $this->errors[] = 'champ content trop long (500 max)';
            }
        }


        if (count($this->errors) > 0) {
            return false;
        }
        return true;
    }

	/**
	 * @return array
	 */
    public function findAll()
    {
        $dbh = Connection::get();
        $stmt = $dbh->query("select * from messages");
        // recupere les users et fout le resultat dans une variable sous forme de tableau de tableaux
        $messages = $stmt->fetchAll(PDO::FETCH_CLASS);
        return $messages;
    }

    public function save($data)
    {
        if ($this->validate($data)) {
//            if(isset($data['id']) && !empty($data['id'])){
//                // update
//            }

            /* syntaxe avec preparedStatements */
            $dbh = Connection::get();
            $sql = "insert into messages (user_id, content) values (:user_id, :content)";
            $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            if ($sth->execute(array(
                ':user_id' => $data['user_id'],
                ':content' => trim($data['content'])
            ))) {
                return true;
            } else {
                // ERROR
                // put errors in $session
                $this->errors['pas reussi a creer le message'];
            }
        }

        return false;
    }

}
