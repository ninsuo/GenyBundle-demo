<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Base\BaseController;
use GenyBundle\Entity\Form;
use GenyBundle\Form\FormType;

class DefaultController extends BaseController {

    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $listForms = $em->getRepository('GenyBundle:Form')->findBy(
                array(), // Pas de critère
                array('title' => 'asc')
        );

        return [
            'listForms' => $listForms
        ];
    }

    /**
     * @Route("/create/form", name="create_form")
     * @Template()
     */
    public function createFormAction(Request $request) {

        ini_set('display_errors', 1);

        $form_entity = new Form();

        $form = $this->get('form.factory')->create(FormType::class, $form_entity);


        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($form_entity);

            $em->flush();


            //$request->getSession()->getFlashBag()->add('notice', 'Form created');// to implement ?


            return $this->redirectToRoute('build_form', array('id' => $form_entity->getId()));
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route(
     * "/build/form/{id}",
     *  name="build_form",
     *  requirements = {
     *     "id" = "^\d+$"
     *                }
     * )
     * @Template()
     */
    public function buildFormAction(Request $request, $id) {

        ini_set('display_errors', 1);

        return [
            'id' => $id
        ];
    }

}
