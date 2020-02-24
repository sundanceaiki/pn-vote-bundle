<?php declare(strict_types=1);

namespace VoteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoteType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'votes' => $options['votes']
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'votes'=>0,
            'attr' => ['class' => 'votes']
        ]);
    }

    /**
     * @return string|AbstractType
     */
    public function getParent(): string
    {
        return NumberType::class;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'votes';
    }
}