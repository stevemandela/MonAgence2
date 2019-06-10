<?php

namespace App\Controller;

use App\Entity\Property;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Notification\ContactNotification;


class PropertyController extends AbstractController
{
    

    /**
     * @var PropertyRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(PropertyRepository $repository, ObjectManager $em) {
        $this->repository = $repository;
        $this->em = $em;
    }

	

      
    /**
     * @Route("/biens", name="property.index")
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {


       /* $property = new Property();
        $property->setTitle('Maison Neuve')
                ->setPrice(200000)
                ->setRooms(4)
                ->setBedrooms(3)
                ->setDescription('maison très lumineuse')
                ->setSurface(60)
                ->setFloor(4)
                ->setHeat(1)
                ->setCity('Montpellier')
                ->setAddress('15 boulevard Gambetta')
                ->setPostalCode('34000');

            $em = $this->getDoctrine()->getManager();
            $em->persist($property);
            $em->flush();*/
	   /* $property = $this->repository->findById(1);*/
       /* $property = $this->repository-findAll();
        dump($property);
        $this->em->flush();*/

        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType:: class, $search);
        $form->handleRequest($request);

        $properties = $paginator->paginate(
            $this->repository->findAllVisible($search),
            $request->query->getInt('page', 1), 12
        );

       //$properties = $this->repository->findAllVisible();
        return $this->render('property/index.html.twig', [
			'menu_courant' => 'properties',
            'properties' => $properties,
            'form'   => $form->createView()
            
        ]);
    }


    /**
     * @Route("/biens/{slug}-{id}", name="property.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param Property $property
     * @return Response
     */
    public function show(Property $property, string $slug, Request $request, ContactNotification $notification): Response
    {

        //$property = $this->repository->find($id);
        if ($property->getSlug() !== $slug) {
             return $this->redirectToRoute('property.show', [
                   'id'=> $property->getId(),
                   'slug'=> $property->getSlug()
                ], 301);
        }

        $contact = new Contact();
        $contact->setProperty($property);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notification->notify($contact);
            $this->addFlash('success', 'Votre message a bien été envoyé');
            
            return $this->redirectToRoute('property.show', [
                   'id'=> $property->getId(),
                   'slug'=> $property->getSlug()
                ]);
            
        }
        return $this->render('property/show.html.twig', [
                'property' => $property,
                'menu_courant' => 'properties',
                'form'=>$form->createView()
            ]);
    }

   
}