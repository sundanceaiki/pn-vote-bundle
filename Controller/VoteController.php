<?php declare(strict_types=1);

namespace VoteBundle\Controller;

use VoteBundle\Form\VoteType;
use VoteBundle\Repository\VoteRepository;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VoteController extends AbstractController
{
    /** @var FormFactory */
    private $formFactory;

    /** @var VoteRepository */
    private $voteRepository;

    public function __construct(
        FormFactoryInterface $formFactory,
        VoteRepository $voteRepository
    ) {
        $this->formFactory = $formFactory;
        $this->voteRepository = $voteRepository;
    }

    /**
     * @return string
     */
    protected function getCookieName()
    {
        return $this->getParameter('pn_vote.cookie_name');
    }

    /**
     * @return string
     */
    protected function getCookieLifetime()
    {
        return $this->getParameter('pn_vote.cookie_lifetime');
    }

    /**
     * @return bool
     */
    protected function getCsrfBool()
    {
        return $this->getParameter('pn_vote.csrf');
    }

    /**
     * @param Request $request
     * @param int $resourceId
     * @return bool
     */
    protected function hasVoted($request, $resourceId)
    {
        if ($request->cookies->has($this->getCookieName())) {
            return in_array(
                $resourceId,
                (array)json_decode($request->cookies->get($this->getCookieName()), true),
                true
            );
        }
        return false;
    }

    /**
     * @param $resourceId
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function getFormPositive($resourceId)
    {
        return $this->formFactory
            ->createNamedBuilder('form_votes_positive', FormType::class, null,
                ['csrf_protection' => $this->getCsrfBool()])
            ->setAction($this->generateUrl('vote_vote_votepositive',
                ['resourceId' => $resourceId]))
            ->setMethod('POST')
            ->add('positive', VoteType::class, [
                'constraints' => [new NotBlank()]
            ])
            ->getForm();
    }

    /**
     * @param int $resourceId
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function getFormNegative($resourceId)
    {
        return $this->formFactory
            ->createNamedBuilder('form_votes', FormType::class, null,
                ['csrf_protection' => $this->getCsrfBool()])
            ->setAction($this->generateUrl('vote_vote_votenegative',
                ['resourceId' => $resourceId]))
            ->setMethod('POST')
            ->add('negative', VoteType::class, [
                'constraints' => [new NotBlank()]
            ])
            ->getForm();
    }

    /**
     * @param Form $form
     * @return array
     */
    private static function getFormErrors(Form $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['root'][] = $error->getMessage();
                continue;
            }
            $errors[] = $error->getMessage();
        }

        array_map(function ($child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = self::getFormErrors($child);
            }
        }, $form->all());

        return $errors;
    }

    /**
     * @return Response
     */
    protected function newResponse()
    {
        $response = new Response();
        $response->mustRevalidate();
        $response->setPrivate();
        $response->setMaxAge(0);
        $response->setSharedMaxAge(0);

        return $response;
    }

    /**
     * @param array $votedResourceIds
     */
    protected function sendCookie(array $votedResourceIds)
    {
        $cookie = new Cookie(
            $this->getCookieName(),
            json_encode(array_filter(array_unique($votedResourceIds))),
            strtotime($this->getCookieLifetime())
        );

        $response = $this->newResponse();
        $response->headers->setCookie($cookie);
        $response->sendHeaders();
    }

    protected function checkFormRequest(Request $request, Form $form)
    {
        $form->handleRequest($request);
        if (!$form->isValid() && count($errors = self::getFormErrors($form)) > 0) {
            return new JsonResponse($errors);
        }
        return;
    }

    /**
     * @param Request $request
     * @param int $resourceId
     * @param bool $isPositive
     * @return \VoteBundle\Entity\Vote
     */
    protected function processVote(Request $request, int $resourceId, bool $isPositive)
    {
        $votedResourceIds = [];
        if ($request->cookies->has($this->getCookieName())) {
            $votedResourceIds = (array)json_decode($request->cookies->get($this->getCookieName()), true);
        }

        $voteEntity = $this->voteRepository->findOneBy(['resourceId' => $resourceId]);
        if (!$this->hasVoted($request, $resourceId)) {
            $votedResourceIds[] = $resourceId;
            $voteEntity = $this->voteRepository->addVote($voteEntity, $resourceId, $isPositive);
        }

        $this->sendCookie($votedResourceIds);

        return $voteEntity;
    }

    /**
     * @Route("/vote/{resourceId}", requirements={"resourceId"="\d+"}, methods={"GET"})
     *
     * @param Request $request
     * @param int $resourceId
     * @return Response
     */
    public function votesAction(Request $request, int $resourceId)
    {
        return $this->render('@Vote/votes.html.twig', [
            'formPositive' => $this->getFormPositive($resourceId)->createView(),
            'formNegative' => $this->getFormNegative($resourceId)->createView(),
            'votes' => $this->voteRepository->findOneBy(['resourceId' => $resourceId]),
            'voted' => $this->hasVoted($request, $resourceId),
        ], $this->newResponse());
    }

    /**
     * @Route("/vote/p/{resourceId}", requirements={"resourceId"="\d+"}, methods={"POST"})
     * @param Request $request
     * @param int $contentId
     */
    public function VotePositive(Request $request, int $resourceId)
    {
        $formPositive = $this->getFormPositive($resourceId);
        $formNegative = $this->getFormNegative($resourceId);

        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $this->checkFormRequest($request, $formPositive);
            $voteEntity = $this->processVote($request, $resourceId, true);
        }

        return $this->render('@Vote/votes.html.twig', [
            'formPositive' => $formPositive->createView(),
            'formNegative' => $formNegative->createView(),
            'votes' => $voteEntity,
            'voted' => $this->hasVoted($request, $resourceId)
        ]);
    }

    /**
     * @Route("/vote/n/{resourceId}", requirements={"resourceId"="\d+"}, methods={"POST"})
     * @param Request $request
     * @param int $resourceId
     * @return Response
     */
    public function VoteNegative(Request $request, int $resourceId)
    {
        $formPositive = $this->getFormPositive($resourceId);
        $formNegative = $this->getFormNegative($resourceId);

        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $this->checkFormRequest($request, $formNegative);
            $voteEntity = $this->processVote($request, $resourceId, false);
        }

        return $this->render('@Vote/votes.html.twig', [
            'formPositive' => $formPositive->createView(),
            'formNegative' => $formNegative->createView(),
            'votes' => $voteEntity,
            'voted' => $this->hasVoted($request, $resourceId)
        ]);
    }
}