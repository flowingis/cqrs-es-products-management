Step 5: api lettura prodotto
==============================

In services.yml decommentare:
    - shop.product.read_model.projector
    - shop.product.read_model.repository

Provare con 

curl -X GET http://api.cqrsws.dev/app_dev.php/products/uuid


In \AppBundle\Controller\DefaultController aggiungere:


```
/**
 * @Route("products/{id}", name="get_product")
 */
public function getAction(Request $request)
{
    /** @var Product $productReadModel */
    $productReadModel = $this->get('shop.product.read_model.repository')->find(new ProductId($request->get('id')));

    return new JsonResponse($productReadModel->serialize());
}
```