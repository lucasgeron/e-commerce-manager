<?php
class OrdersController extends ApplicationController {
  protected $beforeAction = array('authenticated' => 'all');

  public function index() {
    $this->orders = Order::all();
    $this->action = '/pedidos/pesquisa';
    $this->subtitle = 'Todos';
  }

  public function search() {
    $this->action = '/pedidos/pesquisa';
    $this->value = $this->params['search'];
    $this->subtitle = 'Pesquisa';
    $this->orders = Order::findByName($this->params['search']);
    $this->render ('index');
  }

  public function delete() {
    $this->order = Order::findById($this->params[':id']);
    if($this->order->getStatus() == 0) {
      $this->order->delete();
      Flash::message('success', "Registro removido com sucesso!");
      $this->redirectTo('/pedidos');
    } else {
      Flash::message('danger', 'Operação não realizada - O pedido está fechado.');
      $this->redirectTo('/pedidos/'.$this->order->getId());
    }
  }

  public function create(){ // BUGA QUANDO ACESSADO INDEVIDAMENTE -> URL
    $this->order = new Order();
    $this->order->setClientId($this->params[':client_id']);
    $this->order->setUserId($this->currentUser()->getId());

    if ($this->order->save()) {
      Flash::message('success', 'Novo Pedido realizado com sucesso!');
      $this->redirectTo('/pedidos/'.$this->order->getId());
    } else {
      Flash::message('danger', 'Cliente invalido. Tente novamente.');
      $this->redirectTo('/clientes');
    }
  }

  public function show() {
    $this->cities = City::all();
    $this->order = Order::findById($this->params[':id']);

    if(empty($this->order)){
      Flash::message('danger', 'Este registro não existe.');
      $this->redirectTo('/pedidos');
    }
    else if($this->order->getuserId() != SessionHelpers::currentUser()->getId()){
      Flash::message('danger', 'Você não pode acessar este registro.');
      $this->redirectTo('/pedidos');
    }
    else if($this->order->getStatus() == 1){
      $this->redirectTo("/pedidos/{$this->order->getId()}/detalhes-do-pedido");
    }
  }

  public function showClosedOrder (){
    $this->cities = City::all();
    $this->order = Order::findById($this->params[':id']);

    if(empty($this->order)){
      Flash::message('danger', 'Este registro não existe.');
      $this->redirectTo('/pedidos');
    }
    else if($this->order->getuserId() != SessionHelpers::currentUser()->getId()){
      Flash::message('danger', 'Você não pode acessar este registro.');
      $this->redirectTo('/pedidos');
    }
    else if($this->order->getStatus() == 0){
      $this->redirectTo("/pedidos/{$this->order->getId()}");
    }
  }

  public function addProduct (){
    $this->order = Order::findById($this->params['order_id']);
    if($this->order->getStatus() == 0) {
      if($this->params['product_id'] == null){
        Flash::message('danger', 'Produto não encontrado.');
        $this->redirectTo('/pedidos/'.$this->order->getId());
      }else {
        $this->order->addProduct($this->params['product_id']);
        $this->redirectTo('/pedidos/'.$this->order->getId());
      }
    }else {
      Flash::message('danger', 'Operação não realizada - O pedido está fechado.');
      $this->redirectTo('/pedidos/'.$this->order->getId());
    }
  }

  public function rmvProduct (){
    $this->order = Order::findById($this->params[':id']);
    if($this->order->getStatus() == 0) {
      $this->order->rmvProduct($this->params[':item']);
      Flash::message('success', "Registro removido com sucesso!");
      $this->redirectTo('/pedidos/'.$this->order->getId());
    }else {
      Flash::message('danger', 'Operação não realizada - O pedido está fechado.');
      $this->redirectTo('/pedidos/'.$this->order->getId());
    }
  }

  public function addAmount (){
    $this->order = Order::findById($this->params[':id']);

    if($this->order->getStatus() == 0) {
      $this->order->addAmount($this->params[':item']);
      $this->redirectTo('/pedidos/'.$this->order->getId());
    }else {
      Flash::message('danger', 'Operação não realizada - O pedido está fechado.');
      $this->redirectTo('/pedidos/'.$this->order->getId());
    }
  }
  public function rmvAmount (){
    $this->order = Order::findById($this->params[':id']);

    if($this->order->getStatus() == 0) {
      $this->order->rmvAmount($this->params[':item']);
      $this->redirectTo('/pedidos/'.$this->order->getId());
    }else {
      Flash::message('danger', 'Operação não realizada - O pedido está fechado.');
      $this->redirectTo('/pedidos/'.$this->order->getId());
    }
  }

  public function closeOrder(){
    $this->order = Order::findById($this->params[':id']);
    $this->order->closeOrder();
    Flash::message('success', "Pedido realizado com sucesso!");
    $this->redirectTo('/pedidos/'.$this->order->getId());
  }
} ?>
