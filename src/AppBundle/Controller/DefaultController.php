<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use AppBundle\API\API;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig');
    }

    /**
     * @param $request Request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api", name="api")
     */
    public function apiAction(Request $request){
        $queryData = new ParameterBag(array_merge($request->query->all(), $request->request->all()));
        if(!$queryData->has('callback')){
            throw new MissingMandatoryParametersException('Mandatory parameter "callback" is missing');
        }
        if(!$queryData->has('action')){
            throw new MissingMandatoryParametersException('Mandatory parameter "action" is missing');
        }
        $service = new API($this->getDoctrine());
        $result = "";
        switch ($queryData->get('action')){
            case 'desc':
                $result = $service->getDesc();
                break;
            case 'tiles':
                if(!$queryData->has('num')){
                    throw new MissingMandatoryParametersException('Mandatory parameter "num" is missing for action "tiles"');
                }
                $result = $service->getTiles($queryData->get('num'));
                break;
            case 'log_action':
                error_log('log_action requested');
                if(!$queryData->has('tile')){
                    throw new MissingMandatoryParametersException('Mandatory parameter "tile" is missing for action "log_action"');
                }
                if(!$queryData->has('decision')){
                    throw new MissingMandatoryParametersException('Mandatory parameter "decision" is missing for action "log_action"');
                }
                $result = $service->getLogAction($queryData->get('tile'), $queryData->get('decision'));
                break;
            default:
                $result = array('error' => 'No valid action!');
        }
        $response = new JsonResponse($result);
        $response->setCallback($queryData->get('callback'));
        return $response;
    }
}
