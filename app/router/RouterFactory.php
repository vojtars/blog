<?php

namespace Vojtars;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList();

		$router[] = $adminRouter = new RouteList('Admin');
		$adminRouter[] = new Route('admin/posts/<blogUrl>', 'Post:list');
		$adminRouter[] = new Route('admin/post/<blogUrl>[/<postId>]', 'Post:edit');
		$adminRouter[] = new Route('admin/category/<blogUrl>[/<categoryId>]', 'Post:category');
		$adminRouter[] = new Route('admin/images/<galleryId>', 'Gallery:detail');
		$adminRouter[] = new Route('admin/<presenter>/<action>', 'Homepage:default');


		$router[] = $frontRouter = new RouteList('Front');
		$frontRouter[] = new Route('prihlasit', 'Sign:in');
		$frontRouter[] = new Route('registrace', 'Sign:up');
		$frontRouter[] = new Route('kontakty', 'About:contact');
		$frontRouter[] = new Route('o-me', 'About:me');
		$frontRouter[] = new Route('projekty', 'About:myProjects');
		$frontRouter[] = new Route('podminky', 'About:terms');
		$frontRouter[] = new Route('blog/<url>[/<catUrl>]', 'Blog:default');
		$frontRouter[] = new Route('hledat/<url>', 'Search:default');
		$frontRouter[] = new Route('<postUrl>', 'Blog:detail');
		$frontRouter[] = new Route('<presenter>/<action>', "Homepage:default");

		return $router;
	}

}
