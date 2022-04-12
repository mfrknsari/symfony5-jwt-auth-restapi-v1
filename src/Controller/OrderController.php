<?php
namespace App\Controller;



use App\Entity\Order;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class OrderController extends AbstractController
{
    /**
     * @Route("/api/order", methods={"GET"}, name="all")
     */
    public function list()
    {
        $users_id = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->findByUserId($users_id);
        $status = count($order)? 200 : 204;


        return $this->json([
            'status' => $status,
            'data' => $order,
        ], $status, [], [
            AbstractNormalizer::GROUPS => 'normal'
        ]);
    }

    /**
     * @Route("/api/order/{id}", methods={"GET"}, name="details")
     */
    public function detail(int $id)
    {
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->findOneBy([
            'id' => $id,
            'user' => $currentUser->getId()
        ]);


        if (empty($order)) {
            return $this->json([
                'status' => 'Order Not Found',
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'data' => $order,
        ], Response::HTTP_OK, [], [
            AbstractNormalizer::GROUPS => 'normal'
        ]);
    }

    /**
     * @Route("/api/order/{id}", methods={"PUT"}, name="update")
     */
    public function update(int $id, Request $request)
    {
        $currentUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository(Order::class);

        $order = $orderRepository->findOneBy([
            'id' => $id,
            'user' => $currentUser->getId()
        ]);

        if (empty($order)) {
            return $this->json([
                'data' => [],
                'status' => 'Order Not Found',
            ], Response::HTTP_NOT_FOUND);
        }


        if (!empty($order->getShippingDate())) {
            return $this->json([
                'data' => [],
                'status' => 'Order Created',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }


        $orderRepository->update($id, [
            'quantity' => $request->get('quantity'),
            'address' => $request->get('address')
        ]);

        return $this->json([
            'status' => 'success',
        ]);
    }

    /**
     * @Route("/api/order", methods={"POST"}, name="insert")
     */
    public function store(Request $request)
    {
        $productId = $request->get('product_id');
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $product = $productRepository->find($productId);

        if (empty($product)) {
            return $this->json([
                'status' => 'Product Not Found',
            ], Response::HTTP_NOT_FOUND);
        }


        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        $order = $orderRepository->insert([
            'orderCode' => time(),
            'product' => $product,
            'quantity' => $request->get('quantity'),
            'address' => $request->get('address'),
            'user' => $this->getUser(),
        ]);

        return $this->json([
            'data' => $order,
            'status' => 'success',
        ], Response::HTTP_CREATED, [], [
            AbstractNormalizer::GROUPS => 'normal'
        ]);
    }
}
