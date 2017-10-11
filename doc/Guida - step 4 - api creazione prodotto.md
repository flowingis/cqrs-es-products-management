Step 4: api creazione prodotto
==============================

Correggere la classe: 
    - \Shop\Product\Aggregate\Product

Questa correzione serve per evitare che non venga salvato l'id dell'aggregato nel campo uuid.

Per provare: curl -X POST http://api.cqrsws.dev/app_dev.php/products

Vedere l'indice products: http://10.10.10.10:9200/products/_search


```
class Product extends EventSourcedAggregateRoot
{
    private $id;

    ...

    protected function applyProductCreated(ProductCreated $event)
    {
        $this->id = $event->getProductId();
    }

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->id;
    }
}

```

Decommentare i servizi:
    - shop.product.command_handler 
    - shop.product.repository

nel file:
    - www/app/config/services.yml


Modifcare la classe:
    - \AppBundle\Controller\DefaultController

```
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
}
``