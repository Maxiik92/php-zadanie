<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\PostsFacade;
use Nette;
use Nette\Application\UI\Form;


final class PhpFormPresenter extends Nette\Application\UI\Presenter
{
  private $facade;

  public function __construct(
    PostsFacade $facade,
  )
  {
    $this->facade = $facade;
    $this->select_options = $this->facade->getSelectOptions();
  }

  public $select_options = [];
  public $formRes = [];

  public $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

  public function renderDefault()
  {
    $this->getComponent('myForm');
  }

  public function renderEdit($id)
  {
    //get base post from table to be able to convert to array
    $post = $this->facade->getPost($id)->toArray();
    //change input formats
    $post['selected_date'] = date_format($post['selected_date'], 'Y-m-d');
    $post['day'] = array_search($post['day'], $this->days);
    //get selectIds and push them in array
    $selectIds = $this->facade->getSelectIds($id);
    foreach ($selectIds as $id) {
      $post['select'][] = $id['select_id'];
    }

    if (!$post) {
      $this->error('Post not Found');
    }
    //fill inputs with data from db
    $this->getComponent('myForm')
      ->setDefaults($post);
  }
  protected function createComponentMyForm($id): Form
  {
    $form = new Form;

    $form->addText('selected_date', 'Select date in the future:')
      ->setHtmlType('Date')
      ->setHtmlAttribute('min', date('Y-m-d'))
      ->setRequired();

    $form->addTextArea('content', 'Insert your content:')
      ->setHtmlAttribute('placeholder', 'Some text...')
      ->setRequired();

    $form->addSelect('day', 'Your favourite day :', $this->days)
      ->setRequired();

    $form->addCheckboxList('select', 'Select options:', $this->select_options)
      ->setRequired();

    $form->addEmail('email', 'Email:')
      ->setHtmlAttribute('placeholder', 'example@email.test')
      ->setRequired();

    $form->addText('color', 'Pick favourite color:')
      ->setHtmlType('color')
      ->setRequired();

    $form->addSubmit('send', 'Submit');
    $form->onSuccess[] = [$this, 'myFormSucceed'];

    return $form;
  }

  public function myFormSucceed(array $data): void
  {
    $postId = $this->getParameter('id');
    $dataWithoutSelect = $data;
    //unset selects so data can be inserted to post table
    unset($dataWithoutSelect['select']);
    $dataWithoutSelect['day'] = $this->days[$dataWithoutSelect['day']];
    //set selectIds for post_select table
    $selectIds = $data['select'];
    if ($postId) {
      $this->facade->updateFormData($dataWithoutSelect, $selectIds, $postId);
    } else {
      $this->facade->insertFormData($dataWithoutSelect, $selectIds);
    }
    $this->forward('Posts:show');
  }
}