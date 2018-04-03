<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Components;


use Nette\Reflection\ClassType;

trait OwnTemplate
{
	/**
	 * @var string
	 */
	private $templateName;

	public function setTemplateName(string $templateName)
	{
		$this->templateName = $templateName;
	}

	/**
	 * @return string
	 * @throws \ReflectionException
	 */
	public function getTemplateFullPath()
	{
		$reflection = new ClassType(__CLASS__);
		return dirname($reflection->getFileName()) . '/templates/' . $this->templateName;
	}
}