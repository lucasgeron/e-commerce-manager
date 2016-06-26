<?php
class City extends Base {

  protected $name;

  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT * FROM cities WHERE id = ?";
    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $city = new City($row);
      return $city;
    }
    return null;
  }

  public static function all() {
    $sql = "SELECT * FROM cities ORDER BY id";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    $states = [];

    if(!$resp) return $states;

    while($city = $statement->fetch(PDO::FETCH_ASSOC)) {
      $cities[] = new City($city);
    }
    return $cities;
  }

}
?>
