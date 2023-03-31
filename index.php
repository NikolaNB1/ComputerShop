<?php
class Prodavnica
{
    private $artikli = [];
    private $balans = 500000;

    public function kupiProizvod($artikl)
    {
        if ($this->balans > $artikl->getCena() * 0.5) {
            $this->artikli[] = $artikl;
            $artikl->setLager($artikl->getLager() + 1);
            $this->balans -= $artikl->getCena() * 0.5;
            printf("<br>Prodavnica je kupila proizvod %s, trenutno stanje %d, balans prodavnice %d", $artikl->model, $artikl->getLager(), $this->balans);
        } else {
            echo "Nemate dovoljno sredstava na racunu";
        }
    }

    public function listaProizvoda()
    {
        foreach ($this->artikli as $artikl) {
            printf("<br>%s x%d", $artikl->model, $artikl->getLager());
        }
    }

    public function prodajProizvod($artikl, $user)
    {
        if (in_array($artikl, $this->artikli) && $artikl->getLager() > 0) {
            $artikl->prodaj();
            $user->kupiProizvod($artikl);
            printf("<br>Proizvod %s je prodat kupcu %s, trenutno stanje je %d", $artikl->model, $user->ime, $artikl->getLager());
            $this->balans += $artikl->getCena();
        } else {
            printf("<br>Proizvod %s nije moguce prodati jer ga nema na lageru", $artikl->model);
        }
    }

    public function ispisiBalans()
    {
        printf("<br>Stanje na racunu prodavnice je %d", $this->balans);
    }

    public function pozajmiProizvod(Artikl $artikl, User $user)
    {
        if ($artikl instanceof Pozajmica && in_array($artikl, $this->artikli) && $artikl->getLager() > 0) {
            $artikl->setLager($artikl->getLager() - 1);
            $user->pozajmiProizvod($artikl);
            printf("<br>Proizvod %s je pozajmljen musteriji %s, stanje na lageru je %d", $artikl->model, $user->ime, $artikl->getLager());
        } else {
            printf("<br>Proizvod %s nije moguce pozajmiti", $artikl->model);
        }
    }

    public function vratiSaPozajmice(Artikl $artikl, User $user)
    {
        if ($artikl instanceof Pozajmica) {
            $artikl->setLager($artikl->getLager() + 1);
            printf("<br>Proizvod %s je vracen sa pozajmice od %s, trenutno stanje %d", $artikl->model, $user->ime, $artikl->getLager());
            $this->balans += $artikl->getCena() * 0.25;
            $user->setBudzet($user->getBudzet() - $artikl->getCena() * 0.25);
        } else {
            printf("<br>Proizvod %s nije moguce vratiti sa pozajmice", $artikl->model);
        }
    }

    public function testirajProizvod(Artikl $artikl)
    {
        if ($artikl instanceof Testiranje && in_array($artikl, $this->artikli) && $artikl->getLager() > 0) {
            $artikl->setLager($artikl->getLager() - 1);
            printf("<br>Proizvod %s je na testiranju, trenutno stanje %d", $artikl->model, $artikl->getLager());
            printf("<br>Serijski broj proizvoda: %d, proizvodjac: %s, model: %s, cena: %d", $artikl->getSerijskiBroj(), $artikl->proizvodjac, $artikl->model, $artikl->getCena());
        } else {
            printf("<br>Proizvod %s nije moguce testirati", $artikl->model);
        }
    }

    public function vratiSaTestiranja(Artikl $artikl)
    {
        if ($artikl instanceof Testiranje) {
            $artikl->setLager($artikl->getLager() + 1);
            printf("<br>Proizvod %s je vracen sa testiranja, trenutno stanje %d", $artikl->model, $artikl->getLager());
        } else {
            printf("<br>Proizvod %s nije moguce vratiti sa testiranja", $artikl->model);
        }
    }
}

abstract class Artikl
{
    private $serijskiBroj;
    public $proizvodjac;
    public $model;
    private $cena;
    private $lager;

    public function __construct($serijskiBroj, $proizvodjac, $model, $cena)
    {
        $this->serijskiBroj = $serijskiBroj;
        $this->proizvodjac = $proizvodjac;
        $this->model = $model;
        $this->cena = $cena;
        $this->lager = 0;
    }

    public function getSerijskiBroj()
    {
        return $this->serijskiBroj;
    }

    public function getLager()
    {
        return $this->lager;
    }

    public function setLager($lager)
    {
        $this->lager = $lager;
    }

    public function prodaj()
    {
        $this->lager -= 1;
    }

    public function getCena()
    {
        return $this->cena;
    }
}

class RAM extends Artikl
{
    public $kapacitet;
    public $frekvencija;

    public function __construct($kapacitet, $frekvencija, $serijskiBroj, $proizvodjac, $model, $cena)
    {
        parent::__construct($serijskiBroj, $proizvodjac, $model, $cena);
        $this->kapacitet = $kapacitet;
        $this->frekvencija = $frekvencija;
    }
}

class CPU extends Artikl implements Testiranje
{
    public $brojJezgara;
    public $frekvencija;

    public function __construct($brojJezgara, $frekvencija, $serijskiBroj, $proizvodjac, $model, $cena)
    {
        parent::__construct($serijskiBroj, $proizvodjac, $model, $cena);
        $this->brojJezgara = $brojJezgara;
        $this->frekvencija = $frekvencija;
    }

    public function testirajProizvod($artikl)
    {
        $artikl->setLager($artikl->getLager() - 1);
    }
}

class HDD extends Artikl
{
    public $kapacitet;

    public function __construct($kapacitet, $serijskiBroj, $proizvodjac, $model, $cena)
    {
        parent::__construct($serijskiBroj, $proizvodjac, $model, $cena);
        $this->kapacitet = $kapacitet;
    }
}

class GPU extends Artikl implements Pozajmica
{
    public $frekvencija;

    public function __construct($frekvencija, $serijskiBroj, $proizvodjac, $model, $cena)
    {
        parent::__construct($serijskiBroj, $proizvodjac, $model, $cena);
        $this->frekvencija = $frekvencija;
    }

    public function pozajmi($artikl)
    {
        $artikl->setLager($artikl->getLager() - 1);
    }

    public function vratiSaPozajmice($artikl)
    {
        $artikl->setLager($artikl->getLager() + 1);
    }
}

interface Pozajmica
{
    public function pozajmi($artikl);
    public function vratiSaPozajmice($artikl);
}

interface Testiranje
{
    public function testirajProizvod($artikl);
}

class User
{
    public $ime;
    public $prezime;
    private $novac;
    private $produkti = [];

    public function __construct($ime, $prezime, $novac)
    {
        $this->ime = $ime;
        $this->prezime = $prezime;
        $this->novac = $novac;
    }

    public function setBudzet($novac)
    {
        $this->novac = $novac;
    }

    public function getBudzet()
    {
        return $this->novac;
    }

    public function ispisiBudzet()
    {
        printf("<br>Budzet kupca %s je %d", $this->ime, $this->novac);
    }

    public function kupiProizvod($artikl)
    {
        $this->produkti[] = $artikl;
        if ($artikl->getCena() > $this->novac) {
            printf("<br>Proizvod %s nije moguce kupiti jer nemate dovoljno sredstava na racunu", $artikl->model);
        } else {
            $this->novac -= $artikl->getCena();
        }
    }

    public function pozajmiProizvod($artikl)
    {
        $this->produkti[] = $artikl;
    }

    public function getProdukti()
    {
        foreach ($this->produkti as $produkt) {
            printf("<br>Kupac %s poseduje %s", $this->ime, $produkt->model);
        }
    }
}

$ram = new RAM(16000, 3200, 1111, "Kingston", "Fury", 3000);
$cpu = new CPU(16, 4200, 2222, "AMD", "Ryzen 5", 10000);
$hdd = new HDD(1000, 3333, "Samsung", "Hard disk", 2000);
$gpu = new GPU(4200, 4444, "Nvidia", "GTX 1050", 50000);

$prodavnica = new Prodavnica();

$nikola = new User("Nikola", "Babic", 120000);

$prodavnica->kupiProizvod($ram);
$prodavnica->kupiProizvod($cpu);
$prodavnica->kupiProizvod($hdd);
$prodavnica->kupiProizvod($gpu);

$prodavnica->listaProizvoda();
$prodavnica->ispisiBalans();
$nikola->ispisiBudzet();

$prodavnica->prodajProizvod($cpu, $nikola);
$prodavnica->ispisiBalans();
$nikola->ispisiBudzet();

$prodavnica->prodajProizvod($hdd, $nikola);
$prodavnica->ispisiBalans();
$nikola->ispisiBudzet();

$prodavnica->prodajProizvod($gpu, $nikola);
$prodavnica->ispisiBalans();
$nikola->ispisiBudzet();

$prodavnica->prodajProizvod($ram, $nikola);
$prodavnica->ispisiBalans();
$nikola->ispisiBudzet();

$nikola->getProdukti();


$prodavnica->kupiProizvod($ram);
$prodavnica->kupiProizvod($cpu);
$prodavnica->kupiProizvod($hdd);
$prodavnica->kupiProizvod($gpu);

$prodavnica->testirajProizvod($cpu);
$prodavnica->vratiSaTestiranja($cpu);

$prodavnica->testirajProizvod($ram);
$prodavnica->vratiSaTestiranja($ram);

$prodavnica->pozajmiProizvod($gpu, $nikola);
$prodavnica->vratiSaPozajmice($gpu, $nikola);
$nikola->ispisiBudzet();
$prodavnica->pozajmiProizvod($hdd, $nikola);
$prodavnica->vratiSaPozajmice($hdd, $nikola);

$prodavnica->ispisiBalans();

$nikola->ispisiBudzet();

// var_dump($prodavnica);
