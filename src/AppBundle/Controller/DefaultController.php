<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Base\BaseController;
use GenyBundle\Entity\Form;
use GenyBundle\Form\FormType;
use GenyBundle\Entity\Data;

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

// To be done by a function. From a provider ? From a Repo ?
        if ($data) {

            $form_entity = $this->get('geny')->getFormEntity($id);

            $em = $this->getDoctrine()->getManager();
            $em->getRepository('GenyBundle:Data');

            $data_object = new Data();
            $data_object->setForm($form_entity);
            $data_object->setData($data);
            $data_object->setUpdatedAt(new \Datetime());

            $em->persist($data_object);
            $em->flush();

            $id = $data_object->getId();


            $request->getSession()->getFlashBag()->add('notice', 'Data Persisted');

            return $this->redirectToRoute('view_data', array('id' => $id));
        }

        return [
            'id' => $id,
            'form' => $form->createView(),
            'data' => $data,
        ];
    }

    /**
     * @Route(
     * "/update/data/{id}",
     *  name="update_data",
     *  requirements = {
     *     "id" = "^\d+$",
     *                }
     * )
     * @Template()
     */
    public function updateDataAction(Request $request, $id) {

        $em = $this->getDoctrine()
                ->getEntityManager();


        $data_entity = $em->getRepository('GenyBundle:Data')
                ->findOneById($id);

        $form = $data_entity->getForm();
        $form_id = $form->getId();

        $data_field = $em->getRepository('GenyBundle:Data')
                ->dataField($id);

        $overloadOptions = array();
        foreach ($data_field as $data_to_catched) {
            $overloadOptions[$data_to_catched['field_name']] = array('data' => $data_to_catched['value']);
        }

        $form2request = $this->get('geny')->getForm($form_id, $overloadOptions);
        $form2request->handleRequest($request);



        $data = null;

        if ($form2request->isValid()) {
            $data = $form2request->getData();
        }

        if ($data) {
            
            $data_entity->setData($data);
            
            $em->persist($data_entity);

            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Data Persisted');

            return $this->redirectToRoute('view_data', array('id' => $id));
            
        }


        return [
            'form' => $form2request->createView(),
            'id' => $form_id
        ];
    }

    /**
     * @Route(
     * "/view/data/{id}",
     *  name="view_data",
     *  requirements = {
     *     "id" = "^\d+$"
     *                }
     * )
     * @Template()
     */
    public function viewDataAction(Request $request, $id) {
        ini_set('display_errors', 1);


        $em = $this->getDoctrine()
                ->getEntityManager();


        $data = $em->getRepository('GenyBundle:Data')
                ->findOneById($id);

        $form = $data->getForm();

        $data_field = $em->getRepository('GenyBundle:Data')
                ->dataField($id);

        return [
            'id' => $id,
            'form' => $form,
            'data_field' => $data_field
        ];
    }

    /**
     * @Route(
     * "/view/list/data/{id}",
     *  name="view_list_data",
     *  requirements = {
     *     "id" = "^\d+$"
     *                }
     * )
     * @Template()
     */
    public function viewListDataFormAction(Request $request, $id) {
        ini_set('display_errors', 1);

        $em = $this->getDoctrine()
                ->getEntityManager();

        $form = $em->getRepository('GenyBundle:Form')->findOneById($id);

        $data_list = $em->getRepository('GenyBundle:Data')
                ->findBy(
                array('form' => $form), array('updatedAt' => 'ASC')
        );

        return [
            'id' => $id,
            'form' => $form,
            'data_list' => $data_list
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
