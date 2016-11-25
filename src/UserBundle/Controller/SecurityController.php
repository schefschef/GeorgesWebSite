<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            return $this->redirectToRoute('g_plateform_home');
        }


        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('UserBundle:Security:login.html.twig', array(
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
        ));
    }
}
/**
 * Created by PhpStorm.
 * User: Flavien
 * Date: 25/11/2016
 * Time: 15:51
 */