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
        public $priceString;
        public function __construct($name, $price) {
            $this->name = $name;
            $this->price = $price;
            $this->priceString = "$" . number_format($price, 2, ".", ",");
        }
    }

    class CartItem {
        public $product;
        public $quantity = 1;
        public $itemTotal;
        public $itemTotalString;
        public function __construct($product) {
            $this->product = $product;
            $this->updateTotal();
        }
        public function changeQuantity($change) {
            $this->quantity += $change;
            $this->updateTotal();
        }
        public function updateTotal() {
            $this->itemTotal = $this->quantity * $this->product->price;
            $this->itemTotalString = "$" . number_format($this->itemTotal, 2, ".", ",");
        }
    }

    class ShoppingCart {
        public $items = array();
        public $cartTotal = 0;
        public $cartTotalString = "$0.00";
        public function addProduct($productName) {
            if (isset($this->items[$productName])) {
                $this->items[$productName]->changeQuantity(1);
            } else {
                $productToAdd = $_SESSION["productList"][$productName];
                $this->items[$productName] = new CartItem($productToAdd);
            }
            $this->updateTotal();
        }
        public function removeProduct($productName) {
            unset($this->items[$productName]);
            $this->updateTotal();
        }
        public function updateTotal() {
            $this->cartTotal = 0;
            foreach ($this->items as $item) {
                $this->cartTotal += $item->itemTotal;
            }
            $this->cartTotalString = "$" . number_format($this->cartTotal, 2, ".", ",");
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
                    echo "<td>" . $product->priceString . "</td>";
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
                    echo "<td>" . $item->product->priceString . "</td>";
                    echo "<td>" . $item->quantity . "</td>";
                    echo "<td>" . $item->itemTotalString . "</td>";
                    echo "<td><a href='index.php?remove=" . $item->product->name . "'> Remove From Cart </a></td>";
                    echo "</tr>";
                };
            ?>
            <tr>
                <td colspan="3">Order Total</td>
                <td><?php echo $_SESSION["shoppingCart"]->cartTotalString ?></td>
            </tr>
        </table>
    </body>
</html>