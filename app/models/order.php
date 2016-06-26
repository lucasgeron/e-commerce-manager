<?php
class Order extends Base {

  private $clientId;
  private $userId;
  private $total;
  private $status;
  private $client;
  private $user;
  private $city;

  public function setClientId($clientId){
    $this->clientId = $clientId;
  }

  public function getClientId(){
    return $this->clientId;
  }

  public function setUserId($userId){
    $this->userId = $userId;
  }

  public function getUserId(){
    return $this->userId;
  }

  public function setStatus($status){
    $this->status = $status;
  }

  public function getStatus(){
    return $this->status;
  }

  public function setTotal($total){
    $this->total = $total;
  }

  public function getClient(){
    return $this->client;
  }

  public function getUser(){
    return $this->user;
  }

  public function getCity(){
    return $this->city;
  }

  public function validates() {
      Validations::notEmpty($this->clientId, 'client_id', $this->errors);
      Validations::notEmpty($this->userId, 'user_id', $this->errors);
  }


  public function getTotal($id){


    $sql = "SELECT
            FORMAT((SUM(item_price*amount)),2) AS total_item
            FROM itens_selling_orders
            WHERE selling_order_id  = ?;";

    $params = array($this->id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);


    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return ($row['total_item']);
    }

    return null;
  }

  public function addProduct($productId){
    $sql = "SELECT amount FROM products
    WHERE id = :product_id";

    $params = array('product_id' => $productId);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) $amount = $row;
    $amount = $amount['amount'];

    if($amount >= 1){
      $sql = "SELECT product_id, selling_order_id FROM itens_selling_orders
      WHERE product_id = :product_id
      AND selling_order_id = :selling_order_id";

      $params = array('selling_order_id' => $this->getId(), 'product_id' => $productId);

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute($params);

      if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) $exist = $row;
        if($exist == null) {
          $sql = "INSERT INTO itens_selling_orders (selling_order_id, product_id, item_price, amount)
          VALUES (:selling_order_id, :product_id, (SELECT selling_price FROM products WHERE id = :product_id), 1);

          UPDATE products
          SET amount = (amount-1)
          WHERE id = :product_id";

          $params = array('selling_order_id' => $this->getId(), 'product_id' => $productId);

          $db = Database::getConnection();
          $statement = $db->prepare($sql);
          $resp = $statement->execute($params);
        }else {
          Order::addAmount($productId);
        }

    } else {
      Flash::message('danger', "Produto Esgotado");
      ViewHelpers::redirectTo('/pedidos/'.$this->id);
    }

  }

  public function rmvProduct($product_id){
    $sql = "SELECT amount FROM itens_selling_orders
    WHERE product_id = :product_id AND selling_order_id = :id";

    $params = array('id' => $this->id, 'product_id' => $product_id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) $amount = $row;
    $amount = $amount['amount'];


    if($amount> 0){}
    $sql = "UPDATE products
            SET amount = (amount+$amount)
            WHERE id = :product_id;

            DELETE FROM itens_selling_orders
            WHERE selling_order_id = :id
            AND product_id = :product_id";


    $params = array('id' => $this->id, 'product_id' => $product_id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);

    $resp = $statement->execute($params);
  }

  public function getProducts(){
    $sql = "SELECT
      p.id, p.name, p.category_id,
    FORMAT((iso.item_price),2) AS selling_price, iso.amount, p.created_at,
    pc.name AS category_name, pc.created_at AS category_created_at
    FROM
      products p, product_categories pc, itens_selling_orders iso
    WHERE
      p.category_id = pc.id AND
      iso.product_id = p.id AND
      iso.selling_order_id = ?
    ORDER BY
      p.name";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute(array($this->getId()));

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {

      $products[] = Product::createProduct($row);
    }
    return $products;
  }

  public function addAmount($productId){

    $sql = "SELECT amount FROM products
    WHERE id = :product_id";

    $params = array('product_id' => $productId);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) $amount = $row;

    if($amount['amount'] > 0){
      $sql = "UPDATE itens_selling_orders
              SET amount = (amount+1)
              WHERE selling_order_id = :id
              AND product_id = :product_id;

              UPDATE products
              SET amount = (amount-1)
              WHERE id = :product_id";

      $params = array('id' => $this->id, 'product_id' => $productId);

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute($params);
    }
    else {
      Flash::message('danger', "Produto Esgotado");
      ViewHelpers::redirectTo('/pedidos/'.$this->id);
    }

  }

  public function rmvAmount($productId){
    $sql = "SELECT amount FROM itens_selling_orders
    WHERE selling_order_id = :id
    AND product_id = :product_id";

    $params = array('id' => $this->id, 'product_id' => $productId);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) $amount = $row;

    if($amount['amount'] > 1){
      $sql = "UPDATE itens_selling_orders
              SET amount = (amount-1)
              WHERE selling_order_id = :id
              AND product_id = :product_id;

              UPDATE products
              SET amount = (amount+1)
              WHERE id = :product_id";

      $params = array('id' => $this->id, 'product_id' => $productId);

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute($params);
    }
    else {
      Order::rmvProduct($productId);
    }
  }

  public function closeOrder(){
    $sql = "UPDATE selling_orders
            SET status = 1,
                total = :total,
                closed_at =:closed_at
            WHERE id = :id ";

    $total = Order::getTotal($this->id);
    $total = doubleval(str_replace(",","",$total));
    $params = array('id' => $this->getId(), 'total' => $total, 'closed_at' => date("Y-m-d H:i:s"));


    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);
  }

  public function save() {

    if (!$this->isvalid()) return false;

    $sql = "INSERT INTO selling_orders (client_id, user_id) VALUES (:client_id, :user_id);";


    $params = array('client_id' => $this->clientId, 'user_id' => $this->userId);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());

    return true;
  }

  public function delete() {
    $db = Database::getConnection();

    $params = array('id' => $this->id);

    $sql = "  DELETE FROM selling_orders WHERE id = :id;
              DELETE FROM itens_selling_orders WHERE selling_order_id = :id";

    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public static function findByName($name) {

    $currentUserId = SessionHelpers::currentUser()->getId();
    $params = array('name' => "%$name%", 'user' => $currentUserId);


    $sql = "SELECT so.id, so.total, so.created_at, so.status, so.client_id, so.user_id,
            u.id AS u_id, u.name AS u_name , u.email AS u_email,
            c.id AS c_id, c.name AS c_name, c.address as c_address, c.address_number AS c_address_number,
            c.district AS c_district, c.address_cep AS c_address_cep, c.phone AS c_phone, c.email AS c_email,
            c.type AS c_type, c.city_id AS c_city_id,
            ci.id AS ci_id, ci.name AS ci_name
            FROM selling_orders so, users u, clients c, cities ci
            WHERE
            so.user_id = :user AND
            u.id = so.user_id AND
            c.id = so.client_id AND
            ci.id = c.city_id AND
            c.name LIKE :name
            ORDER BY
            so.created_at DESC";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $orders = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $orders[] = self::createOrder($row);
    }
    return $orders;
  }

  public static function findById($id) {
    $db = Database::getConnection();

    $sql = "SELECT so.id, so.total, so.created_at, so.status, so.client_id, so.user_id,
            u.id AS u_id, u.name AS u_name , u.email AS u_email,
            c.id AS c_id, c.name AS c_name, c.address as c_address, c.address_number AS c_address_number,
            c.district AS c_district, c.address_cep AS c_address_cep, c.phone AS c_phone, c.email AS c_email,
            c.type AS c_type, c.city_id AS c_city_id,
            ci.id AS ci_id, ci.name AS ci_name
            FROM selling_orders so, users u, clients c, cities ci
            WHERE
            so.id = ? AND
            u.id = so.user_id AND
            c.id = so.client_id AND
            ci.id = c.city_id";
    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {

      return self::createOrder($row);
    }

    return null;
  }

  public static function all() {


    $sql = "SELECT so.id, so.total, so.created_at, so.status, so.client_id, so.user_id,
            u.id AS u_id, u.name AS u_name , u.email AS u_email,
            c.id AS c_id, c.name AS c_name, c.address as c_address, c.address_number AS c_address_number,
            c.district AS c_district, c.address_cep AS c_address_cep, c.phone AS c_phone, c.email AS c_email,
            c.type AS c_type, c.city_id AS c_city_id,
            ci.id AS ci_id, ci.name AS ci_name
            FROM selling_orders so, users u, clients c, cities ci
            WHERE
            so.user_id = ? AND
            u.id = so.user_id AND
            c.id = so.client_id AND
            ci.id = c.city_id
            ORDER BY
            so.created_at DESC";

    $id_current_user = SessionHelpers::currentUser()->getId();
    $params = array($id_current_user);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $orders = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {

      $orders[] = self::createOrder($row);
    }

    return $orders;
  }

  private static function createOrder($row) {
    // TERMINAR DE IMPLEMENTAR O CREATE ORDER
    $order = new Order();
    $order->setId($row['id']);
    $order->setTotal($row['total']);
    $order->setCreatedAt($row['created_at']);
    $order->setStatus($row['status']);
    $order->setClientId($row['client_id']);
    $order->setUserId($row['user_id']);

    $user = new User();
    $user->setId($row['u_id']);
    $user->setName($row['u_name']);
    $user->setEmail($row['u_email']);

    $client = new Client();
    $client->setId($row['c_id']);
    $client->setName($row['c_name']);
    $client->setAddress($row['c_address']);
    $client->setAddressNumber($row['c_address_number']);
    $client->setDistrict($row['c_district']);
    $client->setAddressCep($row['c_address_cep']);
    $client->setPhone($row['c_phone']);
    $client->setEmail($row['c_email']);
    $client->setType($row['c_type']);
    $client->setCityId($row['ci_id']);

    $order->user = $user;
    $order->client = $client;

    return $order;
  }

}
