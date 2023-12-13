<?php

namespace Acme\Jewernico\Command;

class Receipt
{
    private $paymentMethod;
    private $address;
    private $shipping;
    private $tax;
    private $coupon;
    private $subtotal;
    private $products;
    private $total;
    public function __construct($paymentMethod, $address, $shipping, $tax, $coupon, $subtotal, $products)
    {
        $this->paymentMethod = $paymentMethod;
        $this->address = $address;
        $this->shipping = $shipping;
        $this->tax = $tax;
        $this->coupon = $coupon;
        $this->subtotal = $subtotal;
        $this->products = $products;
        $this->total = $subtotal + $shipping + $tax - $coupon;
    }

    public function get()
    {
        $dom = new \PHPHtmlParser\Dom();
        $dom->loadFromFile("../app/resources/templates/receipt.html");

        $dom->find('#date')[0]->firstChild()->setText(date("d/m/Y"));
        $dom->find('#time')[0]->firstChild()->setText(date("H:i:s"));
        $dom->find('#metodo')[0]->firstChild()->setText($this->paymentMethod);
        $dom->find('#direccion')[0]->firstChild()->setText($this->address);

        $details = $dom->find('#detalles')[0]; // Select the tbody element


        for ($i = 0; $i < count($this->products); $i++) {
            $row = new \PHPHtmlParser\Dom\Node\HtmlNode('tr');
            $quantityCell = new \PHPHtmlParser\Dom\Node\HtmlNode('td');
            $quantityCell->setAttribute('style', 'text-align: left;');
            $quantityCell->addChild(new \PHPHtmlParser\Dom\Node\TextNode($this->products[$i]["Cantidad"]));
            $row->addChild($quantityCell);

            $productCell = new \PHPHtmlParser\Dom\Node\HtmlNode('td');
            $productCell->setAttribute('style', 'text-align: center;');
            $productCell->addChild(new \PHPHtmlParser\Dom\Node\TextNode($this->products[$i]["Nombre"]));
            $row->addChild($productCell);

            $priceCell = new \PHPHtmlParser\Dom\Node\HtmlNode('td');
            $priceCell->setAttribute('style', 'text-align: right;');
            $priceCell->addChild(new \PHPHtmlParser\Dom\Node\TextNode('$' . $this->products[$i]["Precio"]));
            $row->addChild($priceCell);

            $details->addChild($row);
        }

        $finalRow = new \PHPHtmlParser\Dom\Node\HtmlNode('tr');
        $finalTdHr = new \PHPHtmlParser\Dom\Node\HtmlNode('td');
        $finalTdHr->setAttribute('colspan', '3');
        $finalHr = new \PHPHtmlParser\Dom\Node\HtmlNode('hr');
        $finalHr->setAttribute('style', 'border: none; border-top: 1px dashed #000;');
        $finalTdHr->addChild($finalHr);
        $finalRow->addChild($finalTdHr);
        $details->addChild($finalRow);

        $shippingCell = $dom->find('#delivery')[0];
        if ($this->shipping == 0) {
            $shippingCell->addChild(new \PHPHtmlParser\Dom\Node\TextNode('Envío gratis'));
        } else {
            $shippingCell->addChild(new \PHPHtmlParser\Dom\Node\TextNode('$' . $this->shipping));
        }

        $taxCell = $dom->find('#tax')[0];
        $taxCell->addChild(new \PHPHtmlParser\Dom\Node\TextNode('$' . $this->tax . ' MXN'));

        $couponCell = $dom->find('#cupon')[0];
        if ($this->coupon == 0) {
            $couponCell->addChild(new \PHPHtmlParser\Dom\Node\TextNode('No aplica'));
        } else {
            $couponCell->addChild(new \PHPHtmlParser\Dom\Node\TextNode('- $' . $this->coupon . ' MXN'));
        }

        $total = $this->subtotal - $this->coupon + $this->tax + $this->shipping;
        $totalCell = $dom->find('#total')[0];
        $totalCell->addChild(new \PHPHtmlParser\Dom\Node\TextNode('$' . $total . ' MXN'));
        return $dom;
    }
}
?>