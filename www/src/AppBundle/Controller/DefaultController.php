<?php

namespace AppBundle\Controller;

use Broadway\UuidGenerator\Rfc4122\Version4Generator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shop\Product\Command\CreateProduct;
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
}
