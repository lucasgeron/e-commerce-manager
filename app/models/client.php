<?php
class Client extends Base {

  protected $name;
  protected $email;
  protected $address;
  protected $addressNumber;
  protected $addressCep;
  protected $district;
  protected $phone;
  protected $cityId;
  protected $type;
  protected $cpf;
  protected $dateOfBirth;
  protected $cnpj;
  protected $socialreason;
  protected $city;
  protected $status;

  public function setCity($city){
    $this->city = $city;
  }
  public function getCity(){
    return $this->city;
  }

  public function setStatus($status){
    $this->status = $status;
  }
  public function getStatus(){
    return $this->status;
  }

  public function setCnpj($cnpj) {
    $cnpj = str_replace('.', '', $cnpj);
    $cnpj = str_replace('-', '', $cnpj);
    $cnpj = str_replace('/', '', $cnpj);
    $this->cnpj = $cnpj;
  }
  public function getCnpj() {
    return $this->cnpj;
  }

  public function setSocialReason($socialreason) {
    $this->socialreason = $socialreason;
  }
  public function getsocialreason() {
    return $this->socialreason;
  }

  public function setCpf($cpf) {
    $cpf = str_replace('.', '', $cpf);
    $cpf = str_replace('-', '', $cpf);
    $this->cpf = $cpf;
  }
  public function getCpf() {
    return $this->cpf;
  }

  public function setDateOfBirth($dateOfBirth) {

    if ($dateOfBirth != null)
    $this->dateOfBirth = $dateOfBirth;
    else
    $this->dateOfBirth = null;
  }
  public function getDateOfBirth() {
    return $this->dateOfBirth;
  }

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

  public function setAddress($address) {
    $this->address = $address;
  }
  public function getAddress() {
    return $this->address;
  }

  public function setDistrict($district) {
    $this->district = $district;
  }
  public function getDistrict() {
    return $this->district;
  }


  public function setAddressNumber($addressNumber) {
    if ($addressNumber != null)
    $this->addressNumber = $addressNumber;
    else
    $this->addressNumber = null;
  }
  public function getAddressNumber() {
    return $this->addressNumber;
  }

  public function setAddressCep($addressCep) {
    $addressCep = str_replace('-', '', $addressCep);
    $this->addressCep = $addressCep;
  }
  public function getAddressCep() {
    return $this->addressCep;
  }

  public function setPhone($phone) {
    $phone = str_replace('(', '', $phone);
    $phone = str_replace(')', '', $phone);
    $phone = str_replace('-', '', $phone);
    $phone = str_replace(' ', '', $phone);

    $this->phone = $phone;

  }
  public function getPhone() {
    return $this->phone;
  }

  public function setCityId($cityId) {
    $this->cityId = $cityId;
  }
  public function getCityId() {
    return $this->cityId;
  }

  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }

  public function validates() {
    Validations::notEmpty($this->cityId, 'city_cityId', $this->errors);

    /* Como o campo é único é necessário atualizar caso não tenha mudado*/
    if ($this->newRecord() || $this->changedFieldValue('name', 'clients')) {
      Validations::notEmpty($this->name, 'name', $this->errors);
      Validations::uniqueField($this->name, 'name', 'clients', $this->errors);
    }
  }

  public function save() {
    if (!$this->isvalid()) return false;

    $sql = "INSERT INTO
    clients (name, email, phone, address , address_number, district, address_cep, city_id, user_id)
    VALUES
    (:name, :email, :phone, :address, :address_number, :district, :address_cep, :city_id, :user_id);";

    $params = array('name' => $this->name, 'email' => $this->email, 'phone' => $this->phone,
    'city_id' => $this->cityId, 'address' => $this->address, 'address_number' => $this->addressNumber, 'district' => $this->district, 'address_cep' => $this->addressCep,
    'user_id' => SessionHelpers::currentUser()->getId());

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());

    if($this->cpf){ // PESSOA FISICA -> se tiver cpf é pessoa fisica, senão, verifica se tem cnpj.
      $sql = "INSERT INTO
      clients_pf (cpf, date_of_birth, client_id)
      VALUES
      (:cpf, :date_of_birth, :client_id);

      UPDATE clients
      SET type = 1
      WHERE (clients.id = :client_id);";

      $params = array('cpf' => $this->cpf, 'date_of_birth' => $this->dateOfBirth, 'client_id' => $this->getId());

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute($params);

      $this->setId($db->lastInsertId());
    }
    else if ($this->cnpj){ // PESSOA JURIDICA -> se tiver cnpj é pessoa juridica, senão, nao adiciona nas tabelas
      $sql = "INSERT INTO
      clients_pj (cnpj, social_reason, client_id)
      VALUES
      (:cnpj, :social_reason, :client_id);

      UPDATE clients
      SET type = 2
      WHERE (clients.id = :client_id);";

      $params = array('cnpj' => $this->cnpj, 'social_reason' => $this->socialreason, 'client_id' => $this->getId());

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute($params);

      $this->setId($db->lastInsertId());
    }
    else {
      $sql = "INSERT INTO
      clients_pf (client_id)
      VALUES
      (:client_id)";

      $params = array('client_id' => $this->getId());

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute($params);

      $this->setId($db->lastInsertId());
    }
    return true;
  }

  public function update($data = array()) {
    if( $this->hasNotChange($data) ) return true; // implementado no base

    $this->setData($data);

    if (!$this->isvalid()) return false;

    if ($this->getType()==1){
      $sql = "UPDATE clients, clients_pf SET
      name = :name,
      address = :address,
      address_number = :address_number,
      district = :district,
      address_cep = :address_cep,
      phone = :phone,
      email = :email,
      city_id = :city_id,
      cpf = :cpf,
      date_of_birth = :date_of_birth
      WHERE
      clients.id = :id AND
      clients_pf.client_id = clients.id";

      $params = array('id' => $this->id,
      'name' => $this->name,
      'address' => $this->address,
      'address_number' => $this->addressNumber,
      'district' => $this->district,
      'address_cep' => $this->addressCep,
      'phone' => $this->phone,
      'email' => $this->email,
      'city_id' => $this->cityId,
      'cpf' => $this->cpf,
      'date_of_birth' => $this->dateOfBirth);
    }
    else {
      $sql = "UPDATE clients, clients_pj SET
      name = :name,
      address = :address,
      address_number = :address_number,
      district = :district,
      address_cep = :address_cep,
      phone = :phone,
      email = :email,
      city_id = :city_id,
      cnpj = :cnpj,
      social_reason = :social_reason
      WHERE
      clients.id = :id AND
      clients_pj.client_id = clients.id";

      $params = array('id' => $this->id,
      'name' => $this->name,
      'address' => $this->address,
      'address_number' => $this->addressNumber,
      'district' => $this->district,
      'address_cep' => $this->addressCep,
      'phone' => $this->phone,
      'email' => $this->email,
      'city_id' => $this->cityId,
      'cnpj' => $this->cnpj,
      'social_reason' => $this->socialreason);
    }

    $db = Database::getConnection();
    $statement = $db->prepare($sql);

    return $statement->execute($params);
  }

  public static function findById($id) {
    $sql = "SELECT
    *
    FROM
    clients
    WHERE
    clients.id = ?";

    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {

      $client = self::createClient($row);
    }
    if($client->type == null) return false;
    if ($client->getType()==1){
      $sql = "SELECT
      clients.id, name, address, address_number, district, address_cep, phone, email,
      type, created_at, city_id, user_id, cpf, date_of_birth, status
      FROM
      clients, clients_pf
      WHERE
      clients.id = ? AND
      clients_pf.client_id = clients.id";
    } else {
      $sql = "SELECT
      clients.id, name, address, address_number, district, address_cep, phone, email,
      type, created_at, city_id, user_id, cnpj, social_reason, status
      FROM
      clients, clients_pj
      WHERE
      clients.id = ? AND
      clients_pj.client_id = clients.id";
    }

    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return self::createClient($row);
    }
    return null;
  }

  public static function findByName($name) {
    $params = array('name' => "%$name%");

    $sql = "SELECT
    id, name
    FROM
    clients
    WHERE
    name LIKE :name AND status = 0
    ORDER BY
    name";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $clients = [];

    if(!$resp) return $clients;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $clients[] = self::createClient($row);
    }
    return $clients;
  }

  public static function all() {

    $sql = "SELECT
    *
    FROM
    clients
    WHERE status = 0
    ORDER BY
    name";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    $clients = [];

    if(!$resp) return $clients;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $clients[] = self::createClient($row);
    }
    return $clients;
  }

  public function delete() {
    $db = Database::getConnection();

    $params = array('id' => $this->id);
    if(Client::hasBond($this->id)){
      $sql = "  UPDATE clients SET status = 1 WHERE id = $this->id";
      $statement = $db->prepare($sql);
      return $statement->execute();

    } else {
      if($this->type == 1){

        $sql = "  DELETE FROM clients_pf WHERE client_id = :id;
        DELETE FROM clients WHERE id = :id";
      }
      else {
        $sql = "  DELETE FROM clients_pj WHERE client_id = :id;
        DELETE FROM clients WHERE id = :id";
      }
    }
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  private static function hasBond($id){
    $sql  = "SELECT * FROM selling_orders, clients where client_id = clients.id AND clients.id = :id ;";
    $params = array('id' => $id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return true;
    }
    return false;
  }

  private static function createClient($row){
    $client = new Client();
    $client->setId($row['id']);
    $client->setName($row['name']);
    $client->setEmail($row['email']);
    $client->setAddress($row['address']);
    $client->setAddressNumber($row['address_number']);
    $client->setDistrict($row['district']);
    $client->setAddressCep($row['address_cep']);
    $client->setPhone($row['phone']);
    $client->setType($row['type']);
    $client->setCreatedAt($row['created_at']);
    $client->setStatus($row['status']);
    $client->setcityId($row['city_id']);
    $client->setCpf($row['cpf']);
    $client->setDateOfBirth($row['date_of_birth']);
    $client->setCnpj($row['cnpj']);
    $client->setSocialReason($row['social_reason']);

    $city = new City();
    $city->setId($row['city_id']);

    $client->city = $city;

    return $client;

  }
} ?>
