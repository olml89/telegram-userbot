<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Http\Web\Form;

use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Http\Web\Request\FormRequest;
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

    /**
     * @return iterable<FormRequest>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (is_null($type = $argument->getType())) {
            return [];
        }

        if ($type === Request::class) {
            return [];
        }

        if (!is_subclass_of($type, FormRequest::class)) {
            return [];
        }

        /** @var class-string<FormRequest> $formRequestClass */
        $formRequestClass = $argument->getType();

        yield new $formRequestClass($this->formFactory, $request);
    }
}
