<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Presenters;

use Nittro\Bridges\NittroUI\Presenter;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter
{

    protected function startup() {
        parent::startup();
        $this->getHttpResponse()->addHeader('Access-Control-Allow-Origin', '*');
        $this->getHttpResponse()->addHeader('Access-Control-Allow-Headers', 'X-Requested-With');
        $this->setDefaultSnippets(['page', 'flashes', 'hp-scripts']);
    }

    public function sendPayload() {
        if ($this->hasFlashSession()) {
            $flashes = $this->getFlashSession();
            $this->payload->flashes = iterator_to_array($flashes->getIterator());
            $flashes->remove();
        }
        parent::sendPayload();
    }

	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\InvalidStateException
	 */
	public function handleSignOut() {
        $this->getUser()->logout(true);
        $this->disallowAjax();
        $this->flashMessage('Úspěšně odhlášeno');
        $this->redirect(':Front:Homepage:default');
    }

}
