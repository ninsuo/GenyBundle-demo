<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Base\BaseController;
use GenyBundle\Entity\Form;
use GenyBundle\Form\FormType;
use GenyBundle\Entity\Data;
use GenyBundle\Entity\DataText;

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
     * "/use/form/{id}",
     *  name="use_form",
     *  requirements = {
     *     "id" = "^\d+$"
     *                }
     * )
     * @Template()
     */
    public function useFormAction(Request $request, $id) {

        // Init
        $set_id = 0;

        // Check if form exists here



        $form = $this->get('geny')->getForm($id);
        $form->handleRequest($request);

        $data = null;
        if ($form->isValid()) {
            $data = $form->getData();
        }

// To be done by a function. From a provider ?
        if ($data) {
            $em = $this->getDoctrine()->getManager();
            foreach ($data as $data_key => $data_value) {

                $data_object = new Data();
                $field = $em->getRepository('GenyBundle:Field')->findOneByName($data_key);
                $data_object->setFieldID($field);
                $data_object->setUpdatedAt(new \Datetime());
                $em->persist($data_object);
                $em->flush();

                $data_id = $data_object->getId();
                if ($set_id == 0) {
                    $set_id = $data_id;
                }

                $data_object->setSetID($set_id);
                $em->persist($data_object);
                $em->flush();



                $field_type = $field->getType();
                switch ($field_type) {
                    case "text":
                        $data_text = $em->getRepository('GenyBundle:DataText')->findOneBy(array('data_id' => $data_id));
                        if (!$data_text) {
                            $data_text = new DataText;
                            $data_text->setDataID($data_object);
                        }
                        $data_text->setText($data_value);
                        $data_text->setUpdatedAt(new \Datetime());
                        $em->persist($data_text);
                        break;
                }
            }

            $em->flush();
        }

        return [
            'id' => $id,
            'form' => $form->createView(),
            'data' => $data,
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
