<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\PostsFacade;
use Nette;


final class PostsPresenter extends Nette\Application\UI\Presenter
{
	private $facade;
	public function __construct(
		PostsFacade $facade,
	)
	{
		$this->facade = $facade;
	}

	public function renderShow()
	{
		$this->template->posts = $this->mergeSelectOptions($this->facade->getAllPosts());
	}

	public function mergeSelectOptions($posts)
	{
		$nicePosts = [];
		$id = 0;
		foreach ($posts as $post) { {
				if ($post->id != $id) {
					$id = $post->id;
					$nicePosts[] = $post;
				} else {
					$lastIndex = count($nicePosts) - 1;
					$nicePosts[$lastIndex]->select_option = $nicePosts[$lastIndex]->select_option . ', ' . $post->select_option;
				}
			}
		}
		return $nicePosts;
	}

}