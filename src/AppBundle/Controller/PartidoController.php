<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Partido;
use AppBundle\Form\Type\PartidoType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PartidoController extends Controller
{
    /**
     * @Route("/", name="resultados")
     */
    public function indexAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $partidos = $em->createQueryBuilder()
            ->select('p')
            ->from('AppBundle:Partido', 'p')
            ->orderBy('p.fechaCelebracion', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('partido/listado.html.twig', [
            'partidos' => $partidos
        ]);
    }

    /**
     * @Route("/partido/nuevo", name="partido_nuevo")
     * @Security("has_role('ROLE_ARBITRO')")
     */
    public function nuevoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $partido = new Partido();
        $em->persist($partido);

        // poner por defecto la fecha hoy
        $partido->setFechaCelebracion(new \DateTime());

        $form = $this->createForm(PartidoType::class, $partido);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('resultados');
        }

        return $this->render('partido/form.html.twig',
            [
                'form' => $form->createView()
            ]);
    }
}
