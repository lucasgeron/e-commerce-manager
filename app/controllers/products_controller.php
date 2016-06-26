<?php
class ProductsController extends ApplicationController{
  protected $beforeAction = array('authenticated' => 'all');

  public function index() {
    $this->products = Product::all();
    $this->action = '/produtos/pesquisa';
    $this->subtitle = 'Todos';
  }

  public function show() {
    $this->product = Product::findById($this->params[':id']);
    if(empty($this->product)){
      Flash::message('danger', 'Este registro não existe.');
      $this->redirectTo('/produtos');
    }
  }

  public function search() {
    $this->action = '/produtos/pesquisa';
    $this->value = $this->params['search'];
    $this->subtitle = 'Pesquisa';
    $this->products = Product::findByName($this->params['search']);
    $this->render ('index');
  }

  public function _new() {
    $this->product = new Product();
    $this->action = '/produtos';
    $this->submit = 'Salvar';
    $this->categories = Category::all();
  }

  public function create(){
    $this->product = new Product($this->params['product']);

    if ($this->product->save()) {
      Flash::message('success', 'Registro realizado com sucesso!');
      $this->redirectTo('/produtos');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->categories = Category::all();
      $this->action = '/produtos';
      $this->submit = 'Salvar';
      $this->render('new');
    }
  }

  public function edit() {
    $this->product = Product::findById($this->params[':id']);
    $this->categories = Category::all();
    $this->action = '/produtos/' . $this->product->getId();
    $this->submit = 'Atualizar';
  }

  public function update() {
    $this->product = Product::findById($this->params[':id']);
    $this->categories = Category::all();
    if ($this->product->update($this->params['product'])) {
      Flash::message('success', "Registro ID: {$this->product->getId()} atualizado com sucesso!");
      $this->render('show');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->action = '/produtos/' . $this->product->getId();
      $this->submit = 'Atualizar';
      $this->render('edit');
    }
  }

  public function delete() {
    $this->product = Product::findById($this->params[':id']);
    $this->product->delete();
    Flash::message('success', "Registro removido com sucesso!");
    $this->redirectTo('/produtos');
  }

  public function autoCompleteSearch(){
    $products = Product::whereNameLikeAsJson($this->params['query']);
    echo $products;
    exit;
  }
} ?>
