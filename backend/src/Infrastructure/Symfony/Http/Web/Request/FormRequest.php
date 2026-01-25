<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Http\Web\Request;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

abstract readonly class FormRequest
{
    protected Request $request;
    protected RequestData $requestData;
    protected FormInterface $form;

    public function __construct(FormFactoryInterface $formFactory, Request $request)
    {
        $this->request = $request;
        $this->requestData = $this->initializeRequestData();
        $this->form = $formFactory->create($this->requestData->getType(), $this->requestData);
    }

    abstract protected function initializeRequestData(): RequestData;

    public function requestData(): RequestData
    {
        return $this->requestData;
    }

    public function isValid(): bool
    {
        $this->form->handleRequest($this->request);

        return $this->form->isSubmitted() && $this->form->isValid();
    }

    /**
     * @return FormErrorIterator<FormError>
     */
    public function getErrors(bool $deep = false, bool $flatten = true): FormErrorIterator
    {
        return $this->form->getErrors($deep, $flatten);
    }

    public function createView(): FormView
    {
        return $this->form->createView();
    }
}
