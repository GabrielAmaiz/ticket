<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use MercadoPago\Preference;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AppServices;
//use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class MercadoPagoAPI extends AbstractController
{
    /**
     * @Route("/mercado-pago-request", name="mercado_pago_request")
     */
    public function requestPago(Request $request, AppServices $services, TranslatorInterface $translator): Response
    {
        //respuesta:
        //https://ticket.gouservis.com/es/api/mercado-pago-request?collection_id=67231503783&collection_status=approved&payment_id=67231503783&status=approved&external_reference=62e58b844b35ade&payment_type=account_money&merchant_order_id=13496777654&preference_id=1548795509-16f2ee66-a997-4478-98ad-30ff71be7695&site_id=MLA&processing_mode=aggregator&merchant_account_id=null


        /*
        query: Symfony\Component\HttpFoundation\ParameterBag {#18 ▼
            #parameters: array:11 [▼
            "collection_id" => "67231503783"
            "collection_status" => "approved"
            "payment_id" => "67231503783"
            "status" => "approved"
            "external_reference" => "62e58b844b35ade"
            "payment_type" => "account_money"
            "merchant_order_id" => "13496777654"
            "preference_id" => "1548795509-16f2ee66-a997-4478-98ad-30ff71be7695"
            "site_id" => "MLA"
            "processing_mode" => "aggregator"
            "merchant_account_id" => "null"
            ]
        }
        
        //dump($request->query->get('status'));
        echo($request->query->get('status')."\n --  ");
        echo($request->query->get('external_reference')."\n --  ");
        echo($request->query->get('payment_id')."\n --  ");
        echo($request->query->get('merchant_order_id')."\n");
        dump($request);
        //die('en desarollo...');
        */

        $datosdelcelu=json_encode($request);
        $datosdelcelu=str_replace(",", "\n", $datosdelcelu);
        $file = fopen("a_test.txt", "a+"); //a
        fwrite($file, "##### INICIO MercadoPago API ".date('m-d-Y h:i:s a', time())." #####" . PHP_EOL);
        fwrite($file, $datosdelcelu . PHP_EOL);
        fwrite($file, $request . PHP_EOL);
        fwrite($file, "################ FIN ################" . PHP_EOL);
        fwrite($file, "" . PHP_EOL);
        fclose($file); 

        if (!is_null($request)){echo("\n --  ");}
        

        // Remove ticket reservations
        $em = $this->getDoctrine()->getManager();
        if (count($this->getUser()->getTicketreservations())) {
            foreach ($this->getUser()->getTicketreservations() as $ticketreservation) {
                $em->remove($ticketreservation);
            }
            $em->flush();
        }

        if ($request->query->get('status') == "approved") {
            /*$payoutRequest = $services->getPayoutRequests(array("reference" => $request->query->get('external_reference'), "status" => 0))->getQuery()->getOneOrNullResult();
            $services->handleSuccessfulPayment($payment->getOrder()->getReference());
            dump($payoutRequest);
            die;

            if (!$payoutRequest) {
                $this->addFlash('error', $translator->trans('The payout request can not be found'));
                return $this->redirectToRoute("dashboard_administrator_payout_requests");
            }

            if ($payoutRequest->getDeletedAt()) {
                $this->addFlash('error', $translator->trans('The payout request has been soft deleted, restore it before canceling it'));
                return $this->redirectToRoute("dashboard_administrator_payout_requests");
            }

            if ($payoutRequest->getStatus() != 0) {
                $this->addFlash('error', $translator->trans('The payout request can not be canceled because it is already processed'));
                return $this->redirectToRoute("dashboard_administrator_payout_requests");
            }

            if ($payoutRequest->getPayment() == null) {
                $this->addFlash('error', $translator->trans('The payout request can not be processed at this moment'));
                return $this->redirectToRoute("dashboard_administrator_payout_requests");
            }*/


            $services->handleSuccessfulPayment($request->query->get('external_reference'));
            $this->addFlash('success', $translator->trans('Your payment has been successfully processed'));
            
            if ($this->isGranted("ROLE_ATTENDEE")) {
                return $this->redirectToRoute("dashboard_attendee_order_details", ['reference' => $request->query->get('external_reference')]);
            } else {
                return $this->redirectToRoute("dashboard_pointofsale_order_details", ['reference' => $request->query->get('external_reference')]);
            }
        } else {
            $services->handleFailedPayment($request->query->get('external_reference'));
            $this->addFlash('error', $translator->trans('Your payment could not be processed at this time'));
            if ($this->isGranted("ROLE_ATTENDEE")) {
                return $this->redirectToRoute("dashboard_attendee_checkout_failure", ["number" => $request->query->get('payment_id')]);
            } else {
                return $this->redirectToRoute("dashboard_pointofsale_index");
            }

            $services->handleFailedPayment($request->query->get('external_reference'), $translator->trans('Your order has been automatically canceled because your ticket reservations has been released'));
            $this->addFlash('notice', $translator->trans('Your order has been automatically canceled because your ticket reservations has been released'));
            if ($this->isGranted("ROLE_ATTENDEE")) {
                return $this->redirectToRoute("dashboard_attendee_checkout_failure", ["number" => $request->query->get('payment_id')]);
            } else {
                return $this->redirectToRoute("dashboard_pointofsale_index");
            }
        }
        $this->addFlash('error', $translator->trans('The order can not be found'));
        return $this->redirectToRoute("dashboard_index");
    }


    /**
     * @Route("/mercado-pago-failure", name="mercado_pago_failure")
     */
    public function failurePago(Request $request, AppServices $services, TranslatorInterface $translator): Response
    {

        $datosdelcelu=json_encode($request);
        $datosdelcelu=str_replace(",", "\n", $datosdelcelu);
        $file = fopen("a_test.txt", "a+"); //a
        fwrite($file, "##### INICIO MercadoPago API Fallo ".date('m-d-Y h:i:s a', time())." #####" . PHP_EOL);
        fwrite($file, $datosdelcelu . PHP_EOL);
        fwrite($file, $request . PHP_EOL);
        fwrite($file, "################ FIN ################" . PHP_EOL);
        fwrite($file, "" . PHP_EOL);
        fclose($file); 
        if (!is_null($request)){echo("\n --  ");}

        if ($request->query->get('status') != "approved") {

            $services->handleFailedPayment($request->query->get('external_reference'));
            $this->addFlash('error', $translator->trans('Your payment could not be processed at this time'));
            if ($this->isGranted("ROLE_ATTENDEE")) {
                return $this->redirectToRoute("dashboard_attendee_checkout_failure", ["number" => $request->query->get('payment_id')]);
            } else {
                return $this->redirectToRoute("dashboard_pointofsale_index");
            }

            $services->handleFailedPayment($request->query->get('external_reference'), $translator->trans('Your order has been automatically canceled because your ticket reservations has been released'));
            $this->addFlash('notice', $translator->trans('Your order has been automatically canceled because your ticket reservations has been released'));
            if ($this->isGranted("ROLE_ATTENDEE")) {
                return $this->redirectToRoute("dashboard_attendee_checkout_failure", ["number" => $request->query->get('payment_id')]);
            } else {
                return $this->redirectToRoute("dashboard_pointofsale_index");
            }
        } 
    }
}
