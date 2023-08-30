<?php

namespace App\Controller\Membership;

use App\Client\CitipoInterface;
use App\Controller\AbstractController;
use App\Form\Member\JoinType;
use App\Form\Member\Model\JoinData;
use App\Form\Member\Model\ResetRequestData;
use App\Form\Member\Model\ResetUpdateData;
use App\Form\Member\ResetRequestType;
use App\Form\Member\ResetUpdateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/members")
 */
class JoinController extends AbstractController
{
    private CitipoInterface $citipo;

    public function __construct(CitipoInterface $citipo)
    {
        $this->citipo = $citipo;
    }

    /**
     * @Route("/join", name="membership_join")
     */
    public function join(Request $request)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        if (!$project = $this->getProject()) {
            throw $this->createNotFoundException();
        }

        $data = new JoinData();

        $form = $this->createForm(JoinType::class, $data, ['membership_settings' => $project->membership]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->citipo->persistContact($this->getApiToken(), $data->createApiPayload('api:'.$project->id));

            return $this->redirectToRoute('membership_join_verify');
        }

        return $this->render('member/join/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/join/verify", name="membership_join_verify")
     */
    public function joinVerify()
    {
        $this->denyUnlessToolEnabled('members_area_account');

        return $this->render('member/join/verify.html.twig');
    }

    /**
     * @Route("/join/confirm/{id}/{token}", name="membership_join_confirm")
     */
    public function joinConfirm(string $id, string $token)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        if (!$this->citipo->confirmAccount($this->getApiToken(), $id, $token)) {
            throw $this->createNotFoundException();
        }

        return $this->redirectToRoute('membership_login', ['register' => '1']);
    }

    /**
     * @Route("/reset", name="membership_reset")
     */
    public function reset(Request $request)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        $data = new ResetRequestData();

        $form = $this->createForm(ResetRequestType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $status = $this->citipo->getContactStatus($this->getApiToken(), $data->email);

            if ('member' === $status->status) {
                $this->citipo->requestReset($this->getApiToken(), $status->id);
            }

            return $this->redirectToRoute('membership_reset_verify');
        }

        return $this->render('member/reset/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset/verify", name="membership_reset_verify")
     */
    public function resetVerify()
    {
        $this->denyUnlessToolEnabled('members_area_account');

        return $this->render('member/reset/verify.html.twig');
    }

    /**
     * @Route("/reset/confirm/{id}/{token}", name="membership_reset_confirm")
     */
    public function resetConfirm(Request $request, string $id, string $token)
    {
        $this->denyUnlessToolEnabled('members_area_account');

        $data = new ResetUpdateData();

        $form = $this->createForm(ResetUpdateType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->citipo->confirmReset($this->getApiToken(), $id, $token, $data->password)) {
                throw $this->createNotFoundException();
            }

            return $this->redirectToRoute('membership_login', ['reset' => '1']);
        }

        if (!$this->citipo->getContact($this->getApiToken(), $id)) {
            throw $this->createNotFoundException();
        }

        return $this->render('member/reset/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
