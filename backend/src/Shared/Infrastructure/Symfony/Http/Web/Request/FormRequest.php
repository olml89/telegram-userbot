<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Http\Web\Request;

use olml89\TelegramUserbot\Backend\Shared\Application\Command;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * @template TCommand of Command
 * @template TData of FormData<TCommand>
 */
abstract readonly class FormRequest
{
    /**
     * @var TData
     */
    protected FormData $formData;

    protected Request $request;
    protected FormInterface $form;

    public function __construct(FormFactoryInterface $formFactory, Request $request)
    {
        $this->request = $request;
        $this->formData = $this->initializeFormData();
        $this->form = $formFactory->create($this->formData->getType(), $this->formData);
    }

    /**
     * @return TData
     */
    abstract protected function initializeFormData(): FormData;

    /**
     * @return ?TCommand
     */
    public function command(): ?Command
    {
        $this->form->handleRequest($this->request);

        if (!$this->form->isSubmitted() || !$this->form->isValid()) {
            return null;
        }

        return $this->formData->validated();
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
