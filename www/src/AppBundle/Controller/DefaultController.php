<?php

namespace AppBundle\Controller;

use Broadway\UuidGenerator\Rfc4122\Version4Generator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shop\Product\Command\CreateProduct;
use Shop\Product\Command\DeleteProduct;
use Shop\Product\Command\UpdateProduct;
use Shop\Product\ReadModel\Product;
use Shop\Product\ValueObject\ProductId;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/products", name="create_product")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $uuidGenerator = new Version4Generator();
        $productId = new ProductId($uuidGenerator->generate());

        $createProduct = new CreateProduct(
            $productId,
            '5707055029608',
            'Nome prodotto: Scaaarpe',
            'http://static.politifact.com.s3.amazonaws.com/subjects/mugs/fake.png',
            'Brand prodotto: Super Scaaaarpe',
            new \DateTimeImmutable('2017-02-14')
        );

        $this->get('broadway.command_handling.simple_command_bus')->dispatch($createProduct);

        return new JsonResponse(['product_id' => (string)$productId], 201);
    }

    /**
     * @Route("products/{id}", name="get_product")
     * @Method({"GET"})
     */
    public function getAction(Request $request)
    {
        /** @var Product $productReadModel */
        $productReadModel = $this->get('shop.product.read_model.repository')->find(new ProductId($request->get('id')));

        return new JsonResponse($productReadModel->serialize());
    }

    /**
     * @Route("/products/{id}", name="update_product")
     * @Method({"PUT"})
     */
    public function updateAction(Request $request)
    {
        $productId = new ProductId($request->get('id'));
        $updateProduct = new UpdateProduct(
            $productId,
            '5707055029609',
            'Nome prodotto: Scaaarpe più belle',
            'http://static.politifact.com.s3.amazonaws.com/subjects/mugs/fake1.png',
            'Brand prodotto: Super Scaaaarpe più belle',
            new \DateTimeImmutable()
        );

        $this->get('broadway.command_handling.simple_command_bus')->dispatch($updateProduct);

        return new JsonResponse($this->generateUrl('get_product', ['id' => (string)$productId]), 204);
    }

    /**
     * @Route("products/{id}", name="delete_product")
     * @Method({"DELETE"})
     */
    public function deleteAction(Request $request)
    {
        $productId = new ProductId($request->get('id'));

        $deleteProduct = new DeleteProduct(
            $productId,
            new \DateTimeImmutable()
        );

        $this->get('broadway.command_handling.simple_command_bus')->dispatch($deleteProduct);

        return new JsonResponse(['product_id' => (string)$productId], 200);
    }
}
