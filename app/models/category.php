<?php
class Category extends Base {

  protected $name;
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

  public function validates() {
    if ($this->newRecord() || $this->changedFieldValue('name', 'product_categories')) {
      Validations::notEmpty($this->name, 'name', $this->errors);
      Validations::uniqueField($this->name, 'name', 'product_categories', $this->errors);
    }
  }

  public function save() {
    if (!$this->isvalid()) return false;

    $sql = "INSERT INTO product_categories (name) VALUES (:name);";
    $params = array('name' => $this->name);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());

    return true;
  }

  public function update($data = array()) {
    if ($data['name'] === $this->name) return true;
    $this->setData($data);

    if (!$this->isvalid()) return false;

    $db = Database::getConnection();
    $params = array('name' => $this->name,'id' => $this->id);

    $sql = "UPDATE product_categories SET name = :name WHERE id = :id";

    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public function delete() {
    $db = Database::getConnection();
    if(Category::hasBond($this->id)){
      $sql = "  UPDATE product_categories SET status = 1 WHERE id = $this->id";

      $statement = $db->prepare($sql);
      return $statement->execute();

    } else {
      $params = array('id' => $this->id);
      $sql = "DELETE FROM product_categories WHERE id = :id";

      $statement = $db->prepare($sql);
      return $statement->execute($params);
    }
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT * FROM product_categories WHERE id = ? AND status = 0";
    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $category = new Category($row);
      return $category;
    }
    return null;
  }

  public static function findByName($name) {
    $sql = "SELECT * FROM product_categories WHERE name LIKE :name AND status = 0 ORDER BY name";
    $params = array('name' => "%$name%");

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $categories = [];

    if(!$resp) return $categories;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $categories[] = new Category($row);
    }
    return $categories;
  }

  public static function all() {
    $sql = "SELECT * FROM product_categories WHERE status =0 ORDER BY name";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    $categories = [];

    if(!$resp) return $categories;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $categories[] = new Category($row);
    }
    return $categories;
  }

  private static function hasBond($id){
    $sql  = "SELECT * FROM products, product_categories where category_id = product_categories.id AND product_categories.id = :id";
    $params = array('id' => $id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return true;
    }
    return false;
  }

}
?>
