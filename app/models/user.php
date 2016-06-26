<?php
class User extends Base {

  private $name;
  private $email;
  private $password;

  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }

  public function setEmail($email) {
    $this->email = $email;
  }
  public function getEmail() {
    return $this->email;
  }

  public function setPassword($password) {
    $this->password= $password;
  }

  public function validates() {
    Validations::notEmpty($this->name, 'name', $this->errors);
    /* Como o campo é único é necessário atualizar caso não tenha mudado*/
    if ($this->newRecord() || $this->changedFieldValue('email', 'users', 'id')) {
      Validations::validEmail($this->email, 'email', $this->errors);
      Validations::uniqueField($this->email, 'email', 'users', $this->errors);
    }
    if ($this->newRecord()) /* Caso a senha seja vazia não deve ser atualizada */
    Validations::notEmpty($this->password, 'password', $this->errors);
  }

  public function save() {
    if (!$this->isvalid()) return false;

    $sql = "INSERT INTO users (name, email, password)
    VALUES (:name, :email, :password);";

    $params = array('name' => $this->name, 'email' => $this->email,
    'password' => $this->cryptographyPassword($this->password));

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());
    return true;
  }

  public function update($data = array()) {
    $this->setData($data);
    if (!$this->isvalid()) return false;

    $db = Database::getConnection();
    $params = array('name' => $this->name,
    'email' => $this->email,
    'id' => $this->id);

    if (empty($this->password)) {
      $sql = "UPDATE users SET name=:name, email=:email WHERE id = :id";
    } else {
      $params['password'] = $this->cryptographyPassword($this->password);
      $sql = "UPDATE users SET name=:name, email=:email, password=:password WHERE id = :id";
    }

    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public function authenticate($password) {
    if ($this->password === $this->cryptographyPassword($password)) {
      SessionHelpers::logIn($this);
      return true;
    }
    return false;
  }

  private function cryptographyPassword($password) {
    return sha1(sha1('dw3'.$password));
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT * FROM users WHERE id = ?"; // ALTERADO DE "WHERE ID para WHERE ID"
    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);

    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $user = new User($row);
      return $user;
    }
    return null;
  }

  public static function findByEmail($email) {
    $db = Database::getConnection();
    $sql = "SELECT * FROM users WHERE email = ?";
    $params = array($email);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $user = new User($row);
      return $user;
    }
    return null;
  }

  public static function all() {
    $sql = "SELECT * FROM users ORDER BY id DESC";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    $users = [];

    if(!$resp) return $users;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $users[] = new User($row);
    }
    return $users;
  }
} ?>
