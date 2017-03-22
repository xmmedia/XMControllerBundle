<?php

namespace XM\ControllerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Controller extends SymfonyController
{
    /**
     * Creates teh form, setting the action and method
     * and then handles the request.
     *
     * @param string  $formClass The form class
     * @param object  $entity    The entity
     * @param Request $request   The current request
     * @param array   $options   Array of options
     * @return Form
     */
    protected function getForm(
        $formClass,
        $entity,
        Request $request,
        array $options = []
    ) {
        $formOptions = $this->getFormOptions($entity, $request, $options);

        $form = $this->createForm($formClass, $entity, $formOptions);
        $form->handleRequest($request);

        return $form;
    }

    /**
     * Compiles the options for passing to the create form.
     *
     * @param object  $entity  The entity
     * @param Request $request The current request
     * @param array   $options Array of options
     * @return array
     */
    protected function getFormOptions(
        $entity,
        Request $request,
        array $options = []
    ) {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'action' => null,
        ]);

        $options = $resolver->resolve($options);

        if (null === $entity->getId()) {
            if ($options['action']) {
                $action = $options['action'];
            } else {
                $route = $request->attributes->get('_route');
                $action = $this->generateUrl($route);
            }

            $method = 'POST';

        } else {
            if ($options['action']) {
                $action = $options['action'];
            } else {
                $route = $request->attributes->get('_route');
                $action = $this->generateUrl(
                    $route,
                    ['id' => $entity->getId()]
                );
            }

            $method = 'PUT';
        }

        $formOptions = [
            'action' => $action,
            'method' => $method,
        ];

        return $formOptions;
    }

    /**
     * Processes the form, including checking if the input is valid.
     * Also persists the entity if it's a new entity and flushes the em.
     * Adds the appropriate flash messages (created, updated or invalid).
     * Returns true if everything has been saved.
     * Returns false if there was an error and the form should be displayed again.
     *
     * @param Form $form The form.
     * @param object $entity The entity
     * @param string $userEntityName The name of entity to use in flash messages
     * @return bool
     */
    protected function processForm(Form $form, $entity, $userEntityName)
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $newEntity = !$em->contains($entity);

            if ($newEntity) {
                $em->persist($entity);
            }
            $em->flush();

            $this->addFlashTrans(
                'success',
                'app.message.entity'.($newEntity ? '_created' : '_updated'),
                ['%name%' => $userEntityName]
            );

            return true;

        } else if ($form->isSubmitted()) {
            $this->addFlashTrans(
                'warning',
                'app.message.validation_errors_continue'
            );
        }

        return false;
    }

    /**
     * Adds a flash message for type, but translates first.
     *
     * @param string $type
     * @param string $id The translation ID
     * @param array $parameters Translation parameters
     */
    protected function addFlashTrans($type, $id, $parameters = [])
    {
        $msg = $this->get('translator')->trans($id, $parameters);

        $this->addFlash($type, $msg);
    }

    /**
     * Creates a flat-ish array of the errors on the form,
     * keyed by their field name.
     * May contain nested arrays of errors if the form has child forms.
     *
     * @param Form $form
     * @return array
     */
    protected function getFormErrors(Form $form)
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getFormErrors($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
}