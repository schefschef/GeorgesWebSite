<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace G\PlateformBundle\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use G\PlateformBundle\Entity\Articles;
use G\PlateformBundle\Form\ImageType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;



class AdvertController extends Controller
{
  public function indexAction($page)
  {
    // On ne sait pas combien de pages il y a
    // Mais on sait qu'une page doit être supérieure ou égale à 1
    if ($page < 1) {
      // On déclenche une exception NotFoundHttpException, cela va afficher
      // une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
      throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
    }
      $em = $this->getDoctrine()->getManager();

      $listLastArticle = $em
          ->getRepository('GPlateformBundle:Articles')
          ->findAll();

      ;


    // Ici, on récupérera la liste des annonces, puis on la passera au template

    // Mais pour l'instant, on ne fait qu'appeler le template
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

  public function addAction(Request $request)
  {

      $article = new Articles();

      $form = $this->get('form.factory')->createBuilder(FormType::class, $article)
          ->add('date', DateType::class, array(
              // render as a single text box
              'widget' => 'single_text',
          ))
          ->add('title',     TextType::class)
          ->add('content',   TextareaType::class)

          ->add('uploadImage', FileType::class, array('label' => 'Image (png file)','required' => false))
          ->add('submit',    SubmitType::class)
          ->getForm()
      ;



      if ($request->isMethod('POST')) {

          $form->handleRequest($request);


          if ($form->isValid()) {

              $file = $article->getUploadImage();
              $fileName = md5(uniqid()).'.'.$file->guessExtension();
              $file->move(
                  $this->getParameter('uploadimage_directory'),
                  $fileName
              );
              $article->setUploadImage($fileName);

              // On l'enregistre notre objet $advert dans la base de données, par exemple
              $em = $this->getDoctrine()->getManager();
              $em->persist($article);
              $em->flush();

              $request->getSession()->getFlashBag()->add('notice', 'Article bien enregistrée.');

              // On redirige vers la page de visualisation de l'annonce nouvellement créée
              return $this->redirect($this->generateUrl('g_plateform_view', array('id' => $article->getId())));
          }
      }
      // À ce stade, le formulaire n'est pas valide car :
      // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
      // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
      return $this->render('@GPlateform/Advert/add.html.twig', array(
          'form' => $form->createView(),
      ));

  }

  public function editAction($id, Request $request)
  {
    // Ici, on récupérera l'annonce correspondante à $id

    // Même mécanisme que pour l'ajout
    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

      return $this->redirectToRoute('g_plateform_view', array('id' => 5));
    }

    return $this->render('GlateformBundle:Advert:edit.html.twig');
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

      $listLastArticle = $em
          ->getRepository('GPlateformBundle:Articles')
          ->findAll();

      ;

      return $this->render('GPlateformBundle:Advert:index.html.twig', array('listArticle'=>$listLastArticle));
  }

  public function songsAction()
  {
   

    return $this->render('GPlateformBundle:Advert:songs.html.twig');
 
  }
}