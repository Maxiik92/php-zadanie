<?php
namespace App\Models;

use Nette;

final class PostsFacade
{

  use Nette\SmartObject;

  private $database;

  public function __construct(Nette\Database\Explorer $database)
  {
    $this->database = $database;
  }

  public function getSelectOptions()
  {
    $res = $this->database->table('select_option');
    $output = [];

    foreach ($res as $value) {
      $output[$value->id] = $value->select_option;
    }
    return $output;
  }

  public function getAllPosts()
  {
    return $this->database->query('SELECT post.id, post.selected_date, post.day, post.content, post.email, post.color, select_option.select_option from post join post_select ON id=post_id join select_option ON select_option.id=select_id;');
  }

  public function updateFormData(array $datawithoutSelect, array $selectIds, string $postId)
  {
    $post = $this->database->table('post')->get($postId);
    $post->update($datawithoutSelect);
    //on update checkbox user is able to choose less or different options, better to delete all from db and insert again as it is only junction table
    $this->database->query('DELETE FROM post_select WHERE post_id = ?', $postId);
    $this->insertSelects($selectIds, $postId);
  }

  public function insertFormData(array $dataWithoutSelect, array $selectIds): void
  {
    $post = $this->database->table('post')->Insert($dataWithoutSelect);
    $postId = $post->id;
    $this->insertSelects($selectIds, $postId);
  }

  public function insertSelects(array $selectIds, string $postId)
  {
    foreach ($selectIds as $selectId) {
      $this->database->query('INSERT INTO post_select SET ?;', [
        'post_id' => $postId,
        'select_id' => $selectId
      ]);
    }
  }

}