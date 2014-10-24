<?php
abstract class ShopProduct {
const AVAILABLE = 0;
const OUT_OF_STOCK = 1;
}

//new ShopProduct ();

class BookProduct extends ShopProduct {

}

$book = new BookProduct ();
