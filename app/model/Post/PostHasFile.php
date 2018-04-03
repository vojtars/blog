<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;

/**
 * PosHasFile
 *
 * @ORM\Table(name="post_has_file")
 * @ORM\Entity()
 */
class PostHasFile
{

	Use Identifier;

	/**
	 * @var Post
	 *
	 * @ORM\ManyToOne(targetEntity="Post")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="post_id", referencedColumnName="id")
	 * })
	 */
	private $post;

	/**
	 * @var File
	 *
	 * @ORM\ManyToOne(targetEntity="File")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="file_id", referencedColumnName="id")
	 * })
	 */
	private $file;

	/**
	 * PosHasFile constructor.
	 * @param Post $post
	 * @param File $file
	 */
	public function __construct(Post $post, File $file)
	{
		$this->post = $post;
		$this->file = $file;
	}

	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}

	/**
	 * @return File
	 */
	public function getFile(): File
	{
		return $this->file;
	}


}
