<?php
/**
 * Created by IntelliJ IDEA.
 * User: alexander
 * Date: 2019-06-30
 * Time: 22:22
 */

namespace App\Controller;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Twig\Environment;

class CustomExceptionController extends ExceptionController
{

    protected $debug;
    protected $twig;
    /**
     * @param bool  $debug Show error (false) or exception (true) pages by default
     */
    public function __construct(Environment $twig,bool $debug)
    {
        $this->twig = $twig;
        $this->debug = $debug;
    }
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $defaultError = "Сервис временно недоступен";
        if ($exception->getStatusCode()==403) {
            $defaultError = "Ошибка доступа";
        }
        return new Response($this->twig->render('exception.html.twig',[
            'code'=>$exception->getStatusCode(),
            'message'=>$exception->getMessage(),
            'error_message'=> $defaultError
        ]));
    }

}