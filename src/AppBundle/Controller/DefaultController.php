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

// To be done by a function. From a provider ? From a Repo ?
        if ($data) {
            $em = $this->getDoctrine()->getManager();
            foreach ($data as $data_key => $data_value) {

                $field = $em->getRepository('GenyBundle:Field')->findOneByName($data_key);
                $field_type = $field->getType();

                switch ($field_type) {
                    case "text":
                        $data_text = new DataText;
                        $data_text->setText($data_value);
                        $data_text->setUpdatedAt(new \Datetime());
                        $em->persist($data_text);
                        $em->flush();

                        $data_object = new Data();
                        $data_object->setFieldID($field);
                        $data_object->setUpdatedAt(new \Datetime());
                        $em->persist($data_object);
                        $em->flush();

                        $data_id = $data_object->getId();
                        if ($set_id == 0) {
                            $set_id = $data_id;
                        }

                        $data_object->setDataTextID($data_text);

                        $data_object->setSetID($set_id);
                        $em->persist($data_object);
                        $em->flush();
                        break;
                }
            }

            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Data Persisted');

            return $this->redirectToRoute('view_data', array('id_form' => $id, 'id' => $set_id));
        }

        return [
            'id' => $id,
            'form' => $form->createView(),
            'data' => $data,
        ];
    }

    /**
     * @Route(
     * "/update/data/form/{id_form}/{id_data}",
     *  name="update_data_form",
     *  requirements = {
     *     "id_form" = "^\d+$",
     *     "id_data" = "^\d+$",
     *                }
     * )
     * @Template()
     */
    public function updateDataFormAction(Request $request, $id_form, $id_data) {

        // Init
        $set_id = $id_data;  // Well, that's pretty ugly !!!! To be improved !!!!!
        // Check if form exists here


        $em = $this->getDoctrine()
                ->getEntityManager();


        $data_set = $em->getRepository('GenyBundle:Data')
                ->dataSet($id_data)
        ;


        $overloadOptions = array();
        foreach ($data_set as $data_to_catched) {
            $overloadOptions[$data_to_catched['name']] = array('data' => $data_to_catched['text']);
        }

        $form = $this->get('geny')->getForm($id_form, $overloadOptions);
        $form->handleRequest($request);

        $data = null;
        //$data= array('question_1'=>'rep1','question_2' => 'rep2');
        if ($form->isValid()) {
            $data = $form->getData();
        }

        // To be done by a function. From a provider ? From a Repo ? From the geny service ?
        if ($data) {

            $em = $this->getDoctrine()->getManager();
            foreach ($data as $data_key => $data_value) {

                foreach ($data_set as $data_set_key => $data_set_value) {

                    if ($data_key == $data_set_value['name']) {
                        $field = $em->getRepository('GenyBundle:Field')->findOneByName($data_key);
                        $field_type = $field->getType();

                        switch ($field_type) {

                            case "text":

                                $data_text = $em->getRepository('GenyBundle:DataText')->findOneById($data_set_value['data_text_id']);

                                $data_text->setText($data_value);
                                $data_text->setUpdatedAt(new \Datetime());
                                $em->persist($data_text);
                                $em->flush();


                                /* To  implement update data, only to persist setUpdatedAt
                                 * *
                                 */
                                break;
                        }
                    }
                }
            }

            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Data Persisted');

            return $this->redirectToRoute('view_data', array('id_form' => $id_form, 'id' => $set_id));
        }

        return [
            'id' => $id_form,
            'form' => $form->createView(),
            'data' => $data,
        ];
    }

    /**
     * @Route(
     * "/view/data/{id_form}/{id}",
     *  name="view_data",
     *  requirements = {
     *     "id" = "^\d+$"
     *                }
     * )
     * @Template()
     */
    public function viewSetDataFormAction(Request $request, $id_form, $id) {
        ini_set('display_errors', 1);


        $em = $this->getDoctrine()
                ->getEntityManager();

        $form = $em->getRepository('GenyBundle:Form')->findOneById($id_form);



        $data = $em->getRepository('GenyBundle:Data')
                ->dataSet($id)
        ;

        return [
            'id' => $id,
            'form' => $form,
            'data' => $data
        ];
    }

    /**
     * @Route(
     * "/view/data/{id}",
     *  name="view_list_data_set",
     *  requirements = {
     *     "id" = "^\d+$"
     *                }
     * )
     * @Template()
     */
    public function viewListSetDataFormAction(Request $request, $id) {
        ini_set('display_errors', 1);

        $em = $this->getDoctrine()
                ->getEntityManager();

        $form = $em->getRepository('GenyBundle:Form')->findOneById($id);

        $data_set_list = $em->getRepository('GenyBundle:Data')
                ->dataSetList($id)
        ;


        return [
            'id' => $id,
            'form' => $form,
            'data_set_list' => $data_set_list
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
