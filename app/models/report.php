<?php
class Report extends Base {

  private $firstDate;
  private $lastDate;

  public function setFirstDate($firstDate){
    $this->firstDate = $firstDate;
  }
  public function getFirstDate(){
    return $this->firstDate;
  }

  public function setLastDate($lastDate){
    $this->lastDate = $lastDate;
  }
  public function getLastDate(){
    return $this->lastDate;
  }

  public static function SellingRanking($firstDate, $lastDate){
    $sql = "SELECT
    users.id, name,
    COUNT(selling_orders.id) AS total_pedidos,
    FORMAT(SUM(selling_orders.total),2) AS total_receita
    FROM selling_orders JOIN users
    WHERE status=1
    AND users.id=selling_orders.user_id
    AND selling_orders.closed_at
    BETWEEN :first_date AND :last_date
    GROUP BY user_id
    ORDER BY SUM(selling_orders.total) DESC, total_pedidos DESC";

    $firstDate = $firstDate . ' 00:00:00';
    $lastDate = $lastDate . ' 23:59:59';

    $params = array('first_date' => $firstDate, 'last_date' => $lastDate);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $reports = [];

    if(!$resp) return $reports;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {

      $reports[] = $row;
    }
    return $reports;
  }

  public static function SellingBalance($firstDate, $lastDate){
    $sql = "SELECT  i.product_id, p.name AS product_name, c.name AS category_name, SUM(i.amount) AS unidades_vendidas
    FROM itens_selling_orders i, selling_orders so, products p, product_categories c
    WHERE so.status = 1 AND selling_order_id=so.id  AND  product_id=p.id AND p.category_id = c.id
    AND so.closed_at BETWEEN :first_date AND :last_date
    GROUP BY i.product_id
    ORDER BY  SUM(i.amount) DESC, p.name ASC, c.name ASC";

    $firstDate = $firstDate . ' 00:00:00';
    $lastDate = $lastDate . ' 23:59:59';
    $params = array('first_date' => $firstDate, 'last_date' => $lastDate);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $reports = [];

    if(!$resp) return $reports;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {

      $reports[] = ($row);
    }
    return $reports;
  }
} ?>
