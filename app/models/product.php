<?php
class Product extends Base {

  private $name;
  private $amount;
  private $sellingprice;
  private $categoryId;
  private $category;
  protected $status;

  public function setStatus($status){
    $this->status = $status;
  }
  public function getStatus(){
    return $this->status;
  }

  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }

  public function setAmount($amount) {
    $this->amount = $amount;
  }
  public function getAmount() {
    return $this->amount;
  }

  public function setSellingPrice($sellingprice){
    $this->sellingprice = $sellingprice;
  }
  public function getSellingPrice() {
    return $this->sellingprice;
  }

  public function setCategoryId ($categoryId) {
    $this->categoryId = $categoryId;
  }
  public function getCategoryId () {
    return $this->categoryId;
  }

  public function setCategory($category) {
    $this->category = $category;
  }
  public function getCategory() {
    return $this->category;
  }

  public function validates() {
    if ($this->newRecord() || $this->changedFieldValue('name', 'products')) {
      Validations::notEmpty($this->name, 'name', $this->errors);
      Validations::uniqueField($this->name, 'name', 'products', $this->errors);
    }
    Validations::notEmpty($this->categoryId, 'category_id', $this->errors);
    Validations::notEmpty($this->amount, 'amount', $this->errors);
    Validations::notEmpty($this->sellingprice, 'sellingprice', $this->errors);
    $this->sellingprice = str_replace(',','.', $this->sellingprice);
    Validations::isNumeric($this->sellingprice, 'sellingprice', $this->errors);
  }

  public function save() {
    if (!$this->isvalid()) return false;

    $sql = "INSERT INTO products (name, category_id, amount, selling_price) VALUES (:name, :category_id, :amount, :selling_price);";



    $params = array('name' => $this->name,
    'category_id' => $this->categoryId,
    'amount' => $this->amount,
    'selling_price' => $this->sellingprice);
    // Debug::Log($params);
    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());

    return true;
  }

  public function update($data = array()) {
    if( $this->hasNotChange($data) ) return true; // implementado no base

    $this->setData($data);

    if (!$this->isvalid()) return false;

    $db = Database::getConnection();
    $params = array('name' => $this->name,'id' => $this->id, 'amount' => $this->amount, 'selling_price' => $this->sellingprice, 'category_id' => $this->categoryId);

    $sql = "UPDATE products SET name = :name,
    category_id = :category_id,
    amount = :amount,
    selling_price = :selling_price
    WHERE id = :id";

    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public function delete() {
    $db = Database::getConnection();
    if(Product::hasBond($this->id)){
      $sql = "  UPDATE products SET status = 1 WHERE id = $this->id";
      $statement = $db->prepare($sql);
      return $statement->execute();
    } else {
      $params = array('id' => $this->id);
      $sql = "DELETE FROM products WHERE id = :id";
      $statement = $db->prepare($sql);
      return $statement->execute($params);
    }
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT
    p.id, p.name, p.category_id,
    FORMAT(p.selling_price,2) AS selling_price, p.amount, p.created_at,
    pc.name AS category_name, pc.created_at AS category_created_at
    FROM
    products p, product_categories pc
    WHERE
    p.category_id = pc.id AND p.id = ? AND p.status = 0
    ORDER BY
    p.name";

    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {

      return self::createProduct($row);
    }
    return null;
  }

  public static function findByName($name) {

    $params = array('name' => "%$name%");

    $sql = "SELECT
    p.id, p.name, p.category_id,
    FORMAT(p.selling_price,2) AS selling_price, p.amount, p.created_at,
    pc.name AS category_name, pc.created_at AS category_created_at
    FROM
    products p, product_categories pc
    WHERE
    p.category_id = pc.id AND p.name LIKE :name and p.status = 0
    ORDER BY
    p.name";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $products[] = self::createProduct($row);
    }
    return $products;
  }

  public static function all() {
    $sql = "SELECT
    p.id, p.name, p.category_id,
    FORMAT(p.selling_price,2) AS selling_price, p.amount, p.created_at,
    pc.name AS category_name, pc.created_at AS category_created_at
    FROM
    products p, product_categories pc
    WHERE
    p.category_id = pc.id AND p.status = 0
    ORDER BY
    p.name";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {

      $products[] = self::createProduct($row);
    }
    return $products;
  }

  public static function whereNameLikeAsJson($name) {
    $params = array('name' => "%$name%");

    $sql = "SELECT
    p.id, p.name, c.name AS category_name
    FROM
    products p, product_categories c
    WHERE
    p.name LIKE :name AND
    c.id = p.category_id AND
    p.status = 0
    ORDER BY
    p.name";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $products[] = array('value' => $row['category_name'] . ' - ' .$row['name'], 'data' => $row['id']);
    }
    $suggestions = array('suggestions' => $products);
    return json_encode($suggestions);
  }

  public static function createProduct($row){
    $product = new Product();
    $product->setId($row['id']);
    $product->setName($row['name']);
    $product->setCategoryId($row['category_id']);
    $product->setAmount($row['amount']);
    $product->setSellingPrice($row['selling_price']);
    $product->setCreatedAt($row['created_at']);
    $product->setStatus($row['status']);

    $category = new Category();
    $category->setId($row['category_id']);
    $category->setName($row['category_name']);
    $category->setCreatedAt($row['category_created_at']);

    $product->setCategory($category);

    return $product;
  }

  private static function hasBond($id){
    $sql  = "SELECT * FROM itens_selling_orders, products where product_id = products.id AND products.id = :id;";
    $params = array('id' => $id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return true;
    }
    return false;
  }
} ?>
