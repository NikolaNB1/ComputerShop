<?php

interface Loanable {
  public function loan();
  public function returnFromLoan();
}

abstract class Product {
  private string $serialNumber;
  public string $manufacturer;
  public string $model;
  private float $price;
  private int $amount;

  public function __construct($serialNumber, $manufacturer, $model, $price)
  {
    $this->serialNumber = $serialNumber;
    $this->manufacturer = $manufacturer;
    $this->model = $model;
    $this->price = $price;
    $this->amount = 0;
  }

  public function getSerialNumber() { return $this->serialNumber; }
  public function getAmount() { return $this->amount; }
  public function getPrice() { return $this->price; }
  public function setAmount(int $amount) {
    $this->amount = $amount;
  }
}

class RAM extends Product implements Loanable {
  private float $capacity;
  private float $frequency;

  public function __construct($serialNumber, $manufacturer, $model, $price, $capacity, $frequency)
  {
    parent::__construct($serialNumber, $manufacturer, $model, $price);
    $this->capacity = $capacity;
    $this->frequency = $frequency;
  }

  public function getCapacity() { return $this->capacity; }
  public function getFrequency() { return $this->frequency; }

  public function loan() {
    $this->setAmount($this->getAmount() - 1);
  }

  public function returnFromLoan() {
    $this->setAmount($this->getAmount() + 1);
  }
}

class CPU extends Product {
  private int $coreNumber;
  private float $frequency;

  public function __construct($serialNumber, $manufacturer, $model, $price, $coreNumber, $frequency)
  {
    parent::__construct($serialNumber, $manufacturer, $model, $price);
    $this->coreNumber = $coreNumber;
    $this->frequency = $frequency;
  }

  public function getCoreNumber() { return $this->coreNumber; }
  public function getFrequency() { return $this->frequency; }
}

class HDD extends Product {
  private float $capacity;

  public function __construct($capacity, $serialNumber, $manufacturer, $model, $price)
  {
    parent::__construct($serialNumber, $manufacturer, $model, $price);
    $this->capacity = $capacity;
  }

  public function getCapacity() { return $this->capacity; }
}

class GPU extends Product {
  private float $frequency;

  public function __construct($serialNumber, $manufacturer, $model, $price, $frequency)
  {
    parent::__construct($serialNumber, $manufacturer, $model, $price);
    $this->frequency = $frequency;
  }

  public function getFrequency() { return $this->frequency; }
}



class Store {
  private $products;
  private float $balance;

  public function __construct()
  {
    $this->products = [];
    $this->balance = 0;
  }

  public function addProduct(Product $product) {
    $this->products[] = $product;
  }

  public function getProducts() { return $this->products; }
  public function getBalance() { return $this->balance; }
  public function setbalance(float $balance) {
    $this->balance = $balance;
  }

  public function canSellProduct(Product $product): bool {
    if(!in_array($product, $this->products) || $product->getAmount() == 0) {
      printf("\n\n Product %s is not loanable!", $product->model);
      return false;
    }
    return true;
  }

  public function writeProductStatistics(Product $product) {
    echo "\n===================";
    printf("\n Current store balance: %f", $this->getBalance());
    printf("\n Products left: %d", $product->getAmount());
    echo "\n==================="; 
  }

  public function sellProduct(Product $product) {
    if(!$this->canSellProduct($product)) return;
    
    $product->setAmount($product->getAmount() - 1);
    $this->balance += $product->getPrice();

    printf("\n %s successfully sold!", $product->model);
    $this->writeProductStatistics($product);
  }

  public function loanProduct(Product $product) {
    if(!$this->canSellProduct($product)) return;
    if(!$product instanceof Loanable) {
      printf("\n\n Product %s is not loanable!", $product->model);
      return;
    }

    $product->loan();
    $loanCharge = $product->getPrice() * 0.25;
    $this->balance += $loanCharge;

    printf("\n %s successfully loaned for $%f", $product->model, $loanCharge);
    $this->writeProductStatistics($product);
  }

  public function returnProduct(Product $product) {
    if($product instanceof Loanable) {
      $product->returnFromLoan();
      printf("\n %s successfully returned from loan", $product->model);
    }
  }
}

$store = new Store();

$hardDisk = new HDD(100, "A124263134", "Toshiba", "S1", 50);
$store->addProduct($hardDisk);

$ram = new RAM("K3452345", "Samsung", "980Pro", 120, 1000, 8000);
$store->addProduct($ram);

$cpu = new CPU("I34634576", "Intel", "I7 7700k", 300, 8, 3600);
$store->addProduct($cpu);

$gpu = new GPU("4352A123", "Nvidia", "GTX 1060", 200, 4000);
$store->addProduct($gpu);



$cpu->setAmount(2);
$store->sellProduct($cpu);
$store->loanProduct($cpu);

$store->loanProduct($ram);
$ram->setAmount(1);
$store->loanProduct($ram);

$store->returnProduct($ram);
$store->loanProduct($ram);
