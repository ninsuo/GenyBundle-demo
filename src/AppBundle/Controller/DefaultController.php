<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Base\BaseController;

class DefaultController extends BaseController
{

    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        return array('views' =>
            array(
                array(
                    'form'   => $this->get('geny')->load('@AppBundle/Resources/geny/pints.json')->createView(),
                    'source' => file_get_contents(__DIR__.'/../Resources/geny/pints.json'),
                ),
                array(
                    'source' => file_get_contents(__DIR__.'/../../../GenyBundle/Resources/geny/types/text.json'),
                ),
                array(
                    'source' => file_get_contents(__DIR__.'/../../../GenyBundle/Resources/geny/types/base.json'),
                ),
                array(
                    'source' => file_get_contents(__DIR__.'/../../../GenyBundle/Resources/geny/types/number.json'),
                ),
                array(
                    'form'   => $this->get('geny')->load('@AppBundle/Resources/geny/option_label.json')->createView(),
                    'source' => file_get_contents(__DIR__.'/../Resources/geny/option_label.json'),
                ),
                array(
                    'form'   => $this->get('geny')->load('@AppBundle/Resources/geny/option_trim.json')->createView(),
                    'source' => file_get_contents(__DIR__.'/../Resources/geny/option_trim.json'),
                ),
            )
        );
    }

}
