<?php
/**
 * ApiController.php
 *
 * API Controller
 *
 * @category Controller
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\User;
use App\Entity\Hosting;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;


/**
 * Class ApiController
 *
 * @Route("/api")
 */
class ApiController extends FOSRestController
{
    /**
     * @Rest\Get("/v1/", name="Version", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets basic info to current logged user."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred."
     * )
     *
     * @SWG\Tag(name="Version")
     */
    public function api()
    {
        return new Response(sprintf('Logged in as %s in API v1', $this->getUser()->getUsername()));
    }

    // USER URI's
 
    /**
     * @Rest\Post("/login_check", name="user_login_check")
     *
     * @SWG\Response(
     *     response=200,
     *     description="User was logged in successfully"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not logged in successfully"
     * )
     *
     * @SWG\Parameter(
     *     name="_username",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={
     *     }
     * )
     *
     * @SWG\Parameter(
     *     name="_password",
     *     in="body",
     *     type="string",
     *     description="The password",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="User")
     */
    public function getLoginCheckAction() {}
 
    /**
     * @Rest\Post("/register", name="user_register")
     *
     * @SWG\Response(
     *     response=201,
     *     description="User was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="_name",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_email",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_username",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_password",
     *     in="query",
     *     type="string",
     *     description="The password"
     * )
     *
     * @SWG\Tag(name="User")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $encoder) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
    
        $user = [];
        $message = "";
    
        try {
            $code = 200;
            $error = false;
    
            $name = $request->request->get('_name');
            $email = $request->request->get('_email');
            $username = $request->request->get('_username');
            $password = $request->request->get('_password');
    
            $user = new User();
            $user->setName($name);
            $user->setEmail($email);
            $user->setUsername($username);
            $user->setPlainPassword($password);
            $user->setPassword($encoder->encodePassword($user, $password));
    
            $em->persist($user);
            $em->flush();
    
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the user - Error: {$ex->getMessage()}";
        }
    
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user : $message,
        ];
    
        return new Response($serializer->serialize($response, "json"));
    }

    // HOSTING URI's
 
    /**
     * @Rest\Get("/v1/hosting.{_format}", name="hosting_list_all", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all hostings for current logged user."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all user hostings."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="query",
     *     type="string",
     *     description="The hosting ID"
     * )
     *
     *
     * @SWG\Tag(name="Hosting")
     */
    public function getAllHostingAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $hostings = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            $userId = $this->getUser()->getId();
            $hostings = $em->getRepository("App:Hosting")->findBy([
                "user" => $userId,
            ]);
 
            if (is_null($hostings)) {
                $hostings = [];
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Hostings - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $hostings : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }
 
    /**
     * @Rest\Get("/v1/hosting/{id}.{_format}", name="hosting_list", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets hosting info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The hosting with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The hosting ID"
     * )
     *
     *
     * @SWG\Tag(name="Hosting")
     */
    public function getHostingAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $hosting = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            $hosting_id = $id;
            $hosting = $em->getRepository("App:Hosting")->find($hosting_id);
 
            if (is_null($hosting)) {
                $code = 500;
                $error = true;
                $message = "The hosting does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Hosting - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $hosting : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }
 
    /**
     * @Rest\Post("/v1/hosting.{_format}", name="hosting_add", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="Hosting was added successfully"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error was occurred trying to add new hosting"
     * )
     *
     * @SWG\Parameter(
     *     name="name",
     *     in="body",
     *     type="string",
     *     description="The hosting name",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="Hosting")
     */
    public function addHostingAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $hosting = [];
        $message = "";
        
        try {
            $code = 201;
            $error = false;
            $name = $request->request->get("name", null);
            $user = $this->getUser();
            
            if (!is_null($name)) {
                $hosting = new Hosting();
                $hosting->setName($name);
                $hosting->setUser($user);
                
               $em->persist($hosting);
               $em->flush();
 
           } else {
               $code = 500;
               $error = true;
               $message = "An error has occurred trying to add new hosting - Error: You must to provide a hosting name";
           }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to add new hosting - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 201 ? $hosting : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }
 
    /**
     * @Rest\Put("/v1/hosting/{id}.{_format}", name="hosting_edit", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The hosting was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the hosting."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The hosting ID"
     * )
     *
     * @SWG\Parameter(
     *     name="name",
     *     in="body",
     *     type="string",
     *     description="The hosting name",
     *     schema={}
     * )
     *
     *
     * @SWG\Tag(name="Hosting")
     */
    public function editHostingAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $hosting = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
            $name = $request->request->get("name");
            $hosting = $em->getRepository("App:Hosting")->find($id);
 
            if (!is_null($name) && !is_null($hosting)) {
                $hosting->setName($name);
 
                $em->persist($hosting);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to add new hosting - Error: You must to provide a hosting name or the hosting id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the current hosting - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $hosting : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }
 
    /**
     * @Rest\Delete("/v1/hosting/{id}.{_format}", name="hosting_remove", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="hosting was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the hosting"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The hosting ID"
     * )
     *
     * @SWG\Tag(name="Hosting")
     */
    public function deleteHostingAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $hosting = $em->getRepository("App:Hosting")->find($id);
 
            if (!is_null($hosting)) {
                $em->remove($hosting);
                $em->flush();
 
                $message = "The hosting was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent hosting - Error: The hosting id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current hosting - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }
}
