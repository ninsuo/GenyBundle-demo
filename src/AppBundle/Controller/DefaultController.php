<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Base\BaseController;

class DefaultController extends BaseController {

    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction(Request $request) {
        ini_set('display_errors', 1);

        return [
        ];
    }

    /**
     * @Route("/create/form", name="createForm")
     * @Template()
     */
    public function createFormAction(Request $request) {

        ini_set('display_errors', 1);

        return [
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
