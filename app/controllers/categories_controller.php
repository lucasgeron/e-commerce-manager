<?php
class CategoriesController extends ApplicationController {
  protected $beforeAction = array('authenticated' => 'all');

  public function index() {
    $this->categories = Category::all();
    $this->action = '/categorias/pesquisa';
    $this->subtitle = 'Todas';
  }

  public function show() {
    $this->category = Category::findById($this->params[':id']);
    if(empty($this->category)){
      Flash::message('danger', 'Este registro não existe.');
      $this->redirectTo('/pedidos');
    }
  }

  public function search() {
    $this->action = '/categorias/pesquisa';
    $this->value = $this->params['search'];
    $this->subtitle = 'Pesquisa';
    $this->categories = Category::findByName($this->params['search']);
    $this->render ('index');
  }

  public function _new() {
    $this->category = new Category();
    $this->action = '/categorias';
    $this->submit = 'Salvar';
  }

  public function create(){
    $this->category = new Category($this->params['category']);

    if ($this->category->save()) {
      Flash::message('success', 'Registro realizado com sucesso!');
      $this->redirectTo('/categorias');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->action = '/categorias';
      $this->submit = 'Salvar';
      $this->render('new');
    }
  }

  public function edit() {
    $this->category = Category::findById($this->params[':id']);
    $this->action = '/categorias/' . $this->category->getId();
    $this->submit = 'Atualizar';
  }

  public function update() {
    $this->category = Category::findById($this->params[':id']);

    if ($this->category->update($this->params['category'])) {
      Flash::message('success', "Registro ID: {$this->category->getId()} atualizado com sucesso!");
      $this->render('show');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->action = '/categorias/' . $this->category->getId();
      $this->submit = 'Atualizar';
      $this->render('edit');
    }
  }

  public function delete() {
    $this->category = Category::findById($this->params[':id']);
    $this->category->delete();
    Flash::message('success', "Registro removido com sucesso!");
    $this->redirectTo('/categorias');
  }
} ?>
