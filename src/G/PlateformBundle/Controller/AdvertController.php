<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace G\PlateformBundle\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use G\PlateformBundle\Entity\Articles;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use G\PlateformBundle\Form\ArticleEditType;
use G\PlateformBundle\Form\ArticleType;



class AdvertController extends Controller
{

  public function indexAction($page)
  {

    if ($page < 1) {

      throw new NotFoundHttpException('Page "'.$page.'"inexistante.');
    }

      $em = $this->getDoctrine()->getManager();

      $listLastArticle = $em
          ->getRepository('GPlateformBundle:Articles')
          ->findAll();
      ;


    return $this->render('GPlateformBundle:Advert:index.html.twig', array('listArticle'=>$listLastArticle));
  }

  public function viewAction($id)
  {
      // On récupère le repository
      $repository = $this->getDoctrine()
          ->getManager()
          ->getRepository('GPlateformBundle:Articles')
      ;

      // On récupère l'entité correspondante à l'id $id
      $article = $repository->find($id);


      if (null === $article) {
          throw new NotFoundHttpException("L'article d'id ".$id." n'existe pas.");
      }

    return $this->render('GPlateformBundle:Advert:view.html.twig', array(
      'article' => $article
    ));
  }

    /**
     * @Security("has_role('ROLE_AUTEUR')")
     */

  public function addAction(Request $request)
  {
      $article = new Articles();
      $form   = $this->get('form.factory')->create(ArticleType::class, $article);

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

          $file = $article->getUploadImage();
          $fileName = md5(uniqid()).'.'.$file->guessExtension();
          $file->move(
              $this->getParameter('uploadimage_directory'),
              $fileName
          );
          $article->setUploadImage($fileName);

          $em = $this->getDoctrine()->getManager();
          $em->persist($article);
          $em->flush();
          $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
          return $this->redirectToRoute('g_plateform_view', array('id' => $article->getId()));
      }
      return $this->render('GPlateformBundle:Advert:add.html.twig', array(
          'form' => $form->createView(),
      ));
  }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('GPlateformBundle:Articles')->find($id);
        if (null === $article) {
            throw new NotFoundHttpException("L'article d'id ".$id." n'existe pas.");
        }
        $form = $this->get('form.factory')->create(ArticleEditType::class, $article);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {


            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Article bien modifiée.');
            return $this->redirectToRoute('g_plateform_view', array('id' => $article->getId()));
        }
        return $this->render('GPlateformBundle:Advert:edit.html.twig', array(
            'article' => $article,
            'form'   => $form->createView(),
        ));
    }

  public function deleteAction(Request $request, $id)
  {
      $em = $this->getDoctrine()->getManager();
      $article = $em->getRepository('GPlateformBundle:Articles')->find($id);

      if (null === $article) {
          throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
      }
      // On crée un formulaire vide, qui ne contiendra que le champ CSRF
      // Cela permet de protéger la suppression d'annonce contre cette faille
      $form = $this->get('form.factory')->create();

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          $em->remove($article);
          $em->flush();
          $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");
          return $this->redirectToRoute('g_plateform_home');
      }

      return $this->render('GPlateformBundle:Advert:delete.html.twig', array(
          'article' => $article,
          'form'   => $form->createView(),
      ));

    return $this->render('GPlateformBundle:Advert:delete.html.twig');
  }

  public function newsAction()
  {
      $em = $this->getDoctrine()->getManager();

      $listArticle = $em
          ->getRepository('GPlateformBundle:Articles')
          ->findAll();
      ;

      return $this->render('GPlateformBundle:Advert:news.html.twig', array('listArticle'=>$listArticle));
  }

  public function songsAction()
  {
   

    return $this->render('GPlateformBundle:Advert:songs.html.twig');
 
  }
    public function artAction()
    {


        return $this->render('GPlateformBundle:Advert:art.html.twig');

    }
    public function contactAction()
    {


        return $this->render('GPlateformBundle:Advert:contact.html.twig');
    }

    public function aboutAction()
    {


        return $this->render('GPlateformBundle:Advert:about.html.twig');

    }
    public function showDeleteAction()
    {
        $em = $this->getDoctrine()->getManager();

        $listLastArticle = $em
            ->getRepository('GPlateformBundle:Articles')
            ->findAll();
        ;


        return $this->render('GPlateformBundle:Advert:showDelete.html.twig', array('listArticle'=>$listLastArticle));

    }
}