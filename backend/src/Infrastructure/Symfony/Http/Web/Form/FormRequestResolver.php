<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Http\Web\Form;

use olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Http\Web\Request\FormRequest;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AutoconfigureTag('controller.argument_value_resolver', ['priority' => 50])]
final readonly class FormRequestResolver implements ValueResolverInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === FormRequest::class;
    }

    /**
     * @return iterable<FormRequest>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        /** @var class-string<FormRequest> $formRequestClass */
        $formRequestClass = $argument->getType();

        yield new $formRequestClass($this->formFactory, $request);
    }
}
