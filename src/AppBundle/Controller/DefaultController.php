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
        $resource = new \Fuz\GenyBundle\Data\Resources\Form(
            \Fuz\GenyBundle\Provider\Loader\FileLoader::TYPE_FILE,
            '@AppBundle/Resources/geny/pints.json',
            \Fuz\GenyBundle\Provider\Unserializer\JsonUnserializer::FORMAT_JSON
        );

        ini_set('display_errors', 1);
        $this->get('geny')->prepare($resource);

        \Symfony\Component\VarDumper\VarDumper::dump($resource);

        die();


        /*

        return array('views' =>
            array(
                array(
                    'form'   => $this->get('geny')->getType('@AppBundle/Resources/geny/pints.json')->createView(),
                    'source' => file_get_contents(__DIR__.'/../Resources/geny/pints.json'),
                ),
                array(
                    'source' => file_get_contents(__DIR__.'/../../../../GenyBundle/Resources/geny/types/text.json'),
                ),
                array(
                    'source' => file_get_contents(__DIR__.'/../../../../GenyBundle/Resources/geny/types/base.json'),
                ),
                array(
                    'source' => file_get_contents(__DIR__.'/../../../../GenyBundle/Resources/geny/types/number.json'),
                ),
                array(
                    'form'   => $this->get('geny')->getType('@AppBundle/Resources/geny/label.json')->createView(),
                    'source' => file_get_contents(__DIR__.'/../Resources/geny/label.json'),
                ),
                array(
                    'form'   => $this->get('geny')->getType('@AppBundle/Resources/geny/trim.json')->createView(),
                    'source' => file_get_contents(__DIR__.'/../Resources/geny/trim.json'),
                ),
            )
        );
         */
    }

}
