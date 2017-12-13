<?php
    $products = array(
        array("name" => "Sledgehammer", "price" => 125.75),
        array("name" => "Axe", "price" => 190.50),
        array("name" => "Bandsaw", "price" => 562.13),
        array("name" => "Chisel", "price" => 12.9),
        array("name" => "Hacksaw", "price" => 18.45)
        );

    session_start();

    class Product {
        public $name;
        public $price;
        public function __construct($name, $price) {
            $this->name = $name;
            $this->price = $price;
        }
    }

    class CartItem {
        public $product;
        public $quantity = 1;
        public function __construct($product) {
            $this->product = $product;
        }
        public function getTotal() {
            return $this->quantity * $this->product->price;
        }
    }

    class ShoppingCart {
        public $items = array();
        public function addProduct($productName) {
            if (isset($this->items[$productName])) {
                $this->items[$productName]->quantity++;
            } else {
                $productToAdd = $_SESSION["productList"][$productName];
                $this->items[$productName] = new CartItem($productToAdd);
            }
        }
        public function removeProduct($productName) {
            unset($this->items[$productName]);
        }
        public function getTotal() {
            $total = 0;
            foreach ($this->items as $item) {
                $total += $item->getTotal();
            }
            return $total;
        }
    }

    // The product name used as its identifier to keep this simple.
    // In practice I would ensure each product had its own unique ID.

    if (!isset($_SESSION["productList"])) {
        $_SESSION["productList"] = array();
        foreach($products as $product) {
            $_SESSION["productList"][$product["name"]] = new Product($product["name"], $product["price"]);
        };
    }
  
    if (!isset($_SESSION["shoppingCart"])) {
        $_SESSION["shoppingCart"] = new ShoppingCart();
    }

    if (isset($_GET["add"])) {
        $productName = $_GET["add"];
        $_SESSION["shoppingCart"]->addProduct($productName);
    };

    if (isset($_GET["remove"])) {
        $productName = $_GET["remove"];
        $_SESSION["shoppingCart"]->removeProduct($productName);
    };

    function formatCurrency($amount) {
        return "$" . number_format($amount, 2, ".", ",");
    }

?>
<html>
	<head>
	    <title>Basic Shopping Cart</title>
	</head>
	<body>
        <h2>Available Products</h2>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
            </tr>
            <?php
                foreach($_SESSION["productList"] as $product) {
                    echo "<tr>";
                    echo "<td>" . $product->name . "</td>";
                    echo "<td>" . formatCurrency($product->price) . "</td>";
                    echo "<td><a href='index.php?add=" . $product->name . "'> Add To Cart </a></td>";
                    echo "</tr>";
                };
            ?>
        </table>
        <h2>Cart</h2>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Item Total</th>
            </tr>
            <?php
                foreach($_SESSION["shoppingCart"]->items as $item) {
                    echo "<tr>";
                    echo "<td>" . $item->product->name . "</td>";
                    echo "<td>" . formatCurrency($item->product->price) . "</td>";
                    echo "<td>" . $item->quantity . "</td>";
                    echo "<td>" . formatCurrency($item->getTotal()) . "</td>";
                    echo "<td><a href='index.php?remove=" . $item->product->name . "'> Remove From Cart </a></td>";
                    echo "</tr>";
                };
            ?>
            <tr>
                <td colspan="3">Order Total</td>
                <td><?php echo formatCurrency($_SESSION["shoppingCart"]->getTotal()) ?></td>
            </tr>
        </table>
    </body>
</html>